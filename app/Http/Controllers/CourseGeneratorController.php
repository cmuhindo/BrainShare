<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\GeneratedCourse;
use App\Models\SubtopicContent;
use League\CommonMark\CommonMarkConverter;

class CourseGeneratorController extends Controller
{
    public function showForm()
    {
        return view('generate-course');
    }

    public function generatedCourse()
    {
        return view('generated-course');
    }

    public function viewDashboardGeneratedCourse(Request $request)
        {
            // Get the course based on the passed course_id from the POST data
            $courseId = $request->course_id;
            // Retrieve the course from the database with related subtopics (if eager loading is used)
            $generatedCourse = GeneratedCourse::findOrFail($courseId);

            // Decode the JSON content and validate
            $courseContent = json_decode($generatedCourse->json_content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Log invalid JSON for easier debugging
                \Log::error('Invalid JSON content in course: ' . json_last_error_msg());
                abort(500, 'Invalid JSON in course content');
            }

            // Fetch all subtopics with existing content for the given course ID
            // Ensure that SubtopicContent model is correctly related and contains valid content
            $subtopicsWithContent = \App\Models\SubtopicContent::where('generated_course_id', $courseId)->pluck('subtopic_title')->toArray();

            // Log for debugging purposes
            \Log::info('Subtopics With Content:', $subtopicsWithContent);

            // Optionally, check if subtopics are retrieved successfully
            if (empty($subtopicsWithContent)) {
                \Log::warning('No subtopics with content found for course ID: ' . $courseId);
            }

            // Pass data to the view
            return view('generated-course', [
                'course' => $generatedCourse,
                'courseContent' => $courseContent,
                'subtopicsWithContent' => $subtopicsWithContent,
            ]);
        }



    /**
     * Generate text from Gemini API based on a provided prompt.
     */
    public function generateTextFromPrompt(Request $request)
    {
        $prompt = $request->input('prompt');

        // Validate the prompt
        if (empty($prompt)) {
            return response()->json(['error' => 'Prompt is required'], 400);
        }

        try {
            // Get the API key from the .env file
            $apiKey = env('GOOGLE_API_KEY');

            Log::info('API Key:', ['key' => $apiKey]);

            // Make HTTP request to Google Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => json_encode($prompt), // Convert array to JSON string
                            ],
                        ],
                    ],
                ],
            ]);

            // Log the response for debugging purposes
            Log::info('Gemini API Response:', $response->json());

            // Check if the request was successful
            if ($response->successful()) {
                $responseData = $response->json(); // Get the full JSON response

                // Extract the generated text content from the response
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'];

                    // Return the generated text as a JSON response
                    return response()->json(['generatedText' => $generatedText], 200);

                } else {
                    return response()->json(['error' => 'No content generated'], 500);
                }

            } else {
                return response()->json(['error' => 'Failed to generate content. Error: ' . $response->status()], 500);
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error generating content:', ['exception' => $e]);

            return response()->json(['error' => 'Error generating content. Please try again later.'], 500);
        }
    }

    
    ///////////////////////////////////////
        

    public function generateCourse(Request $request)
    {
        // Retrieve inputs from the request
        $mainTopic = $request->input('title');
        $userId = auth()->id(); // Assuming the user is authenticated
        $academicLevel = $request->input('class');
        $subscriptionId = '1';//$subscriptionId = $request->input('subscription_id'); // Pass this as part of the request if applicable

        // Validate inputs
        if (empty($mainTopic)) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Please fill in all required fields.']);
        }

        // Construct the prompt for Gemini API
        $prompt = "Generate a course outline for the course title: {$mainTopic} for a {$academicLevel} student. Include topics and subtopics.";

        try {
            // Get the API key from the .env file
            $apiKey = env('GOOGLE_API_KEY');

            // Make HTTP request to Google Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => json_encode($prompt),
                            ],
                        ],
                    ],
                ],
            ]);

            // Log the response for debugging purposes
            \Log::info('Gemini API Response:', $response->json());

            if ($response->successful()) {
                $responseData = $response->json();

                // Extract the generated text content from the response
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'];

                    // Store the generated course in the database
                    $generatedCourse = \App\Models\GeneratedCourse::create([
                        'user_id' => $userId,
                        'subscription_id' => $subscriptionId,
                        'course_title' => $mainTopic,
                        'class' => $academicLevel, 
                        'course_description' => 'Auto-generated course outline.',
                        'course_content' => $generatedText,
                    ]);

                    // Call another API to generate JSON content from the generated text
                    $jsonPrompt = "Generate a JSON file following this structure:
                    {
                      \"courseTitle\": \"\",
                      \"courseDescription\": \"\",
                      \"class\": \"\",
                      \"topics\": [
                        {
                          \"title\": \"\",
                          \"subtopics\": [
                            \"\",
                            \"\",
                            \"\",
                            \"\"
                          ]
                        }
                      ]
                    }
                    from this content: {$generatedText}";

                    $jsonResponse = Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    [
                                        'text' => json_encode($jsonPrompt),
                                    ],
                                ],
                            ],
                        ],
                    ]);

                    // Log the JSON response
                    \Log::info('Gemini JSON API Response:', $jsonResponse->json());

                    if ($jsonResponse->successful()) {
                        $jsonResponseData = $jsonResponse->json();

                        

                        if (isset($jsonResponseData['candidates'][0]['content']['parts'][0]['text'])) {
                            $rawJson = $jsonResponseData['candidates'][0]['content']['parts'][0]['text'];

                            // Remove code block markers (` ```json ` and ` ``` `)
                            $cleanedJson = preg_replace('/^```json\s*|\s*```$/m', '', $rawJson);

                            // Validate the JSON to ensure it's properly formatted
                            $decodedJson = json_decode($cleanedJson, true);

                            if (json_last_error() === JSON_ERROR_NONE) {
                                // Update the course with the cleaned and valid JSON content
                                $generatedCourse->update([
                                    'json_content' => $cleanedJson,
                                ]);
                            } else {
                                \Log::error('Invalid JSON format detected after cleaning.', [
                                    'error' => json_last_error_msg(),
                                    'raw_json' => $rawJson,
                                ]);
                                throw new \Exception('Failed to generate valid JSON content.');
                            }
                        } else {
                            \Log::error('No JSON content generated from API response.');
                            throw new \Exception('No JSON content generated.');
                        }






                    } else {
                        \Log::error('Failed to generate JSON content.', ['status' => $jsonResponse->status()]);
                    }




                    //return redirect()->back()->with('success', 'Course generated and saved successfully.');

                    // Assuming the course is saved with the ID
                    $courseId = $generatedCourse->id;

                    // Call viewGeneratedCourse() to show the course page instead of redirecting back
                    return $this->viewGeneratedCourse($courseId); // Show the generated course after saving

                } else {
                    throw new \Exception('No content generated.');
                }
            } else {
                throw new \Exception('Failed to generate course content. Error: ' . $response->status());
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error generating course content:', ['exception' => $e]);

            return redirect()->back()->withInput()->withErrors(['error' => 'Error generating course content. Please try again later.']);
        }
    }


    public function generatedCourseDetails(Request $request)
    {
        
        // Check if the form was submitted (POST request)
        if ($request->isMethod('POST')) {
            $courseId = $request->input('courseId');    
            return $this->viewGeneratedCourse($courseId);
        }

        // If it's a GET request, render the form or the page to submit the courseId
        return view('generate-course');

    }




    //////////////////////////////////////


    public function viewGeneratedCourse($courseId)
        {
            // Retrieve the course from the database with related subtopics (if eager loading is used)
            $generatedCourse = GeneratedCourse::findOrFail($courseId);

            // Decode the JSON content and validate
            $courseContent = json_decode($generatedCourse->json_content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Log invalid JSON for easier debugging
                \Log::error('Invalid JSON content in course: ' . json_last_error_msg());
                abort(500, 'Invalid JSON in course content');
            }

            // Fetch all subtopics with existing content for the given course ID
            // Ensure that SubtopicContent model is correctly related and contains valid content
            $subtopicsWithContent = \App\Models\SubtopicContent::where('generated_course_id', $courseId)->pluck('subtopic_title')->toArray();

            // Log for debugging purposes
            \Log::info('Subtopics With Content:', $subtopicsWithContent);

            // Optionally, check if subtopics are retrieved successfully
            if (empty($subtopicsWithContent)) {
                \Log::warning('No subtopics with content found for course ID: ' . $courseId);
            }

            // Pass data to the view
            return view('generated-course', [
                'course' => $generatedCourse,
                'courseContent' => $courseContent,
                'subtopicsWithContent' => $subtopicsWithContent,
            ]);
        }





    ///////////////////////////////////////////



    public function generateSubtopicContent(Request $request)
    {
        $subtopic = $request->input('subtopic');
        $courseId = $request->input('course_id');

        // Retrieve the course record
        $course = GeneratedCourse::find($courseId);

        if (!$course) {
            \Log::error("Course not found for Course ID: $courseId");
            return response()->json(['success' => false, 'message' => 'Invalid course ID provided.']);
        }

        // Extract details from the course record
        $topic = $course->course_title;
        $class = $course->class;

        // Log the inputs for verification
        \Log::info("Generating content for: Subtopic: $subtopic, Topic: $topic, Class: $class, Course ID: $courseId");

        // Construct the prompt
        $prompt = "Provide detailed educational content for the subtopic: {$subtopic} under the topic {$topic} for students in: {$class}. Include explanations, examples, and, where appropriate, include relevant image links and video links (preferably those from sources that are free to use for educational purposes and give credits to the source of the link).";

        //add for class S.4

        try {
            $apiKey = env('GOOGLE_API_KEY');
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => json_encode($prompt),
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $responseData['candidates'][0]['content']['parts'][0]['text'];

                    \App\Models\SubtopicContent::updateOrCreate(
                        ['generated_course_id' => $courseId, 'subtopic_title' => $subtopic],
                        ['content' => $content]
                    );

                    return response()->json(['success' => true, 'message' => 'Content generated successfully.']);
                }
            }

            throw new \Exception('Failed to generate subtopic content.');
        } catch (\Exception $e) {
            \Log::error('Error generating subtopic content:', ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Failed to generate subtopic content.']);
        }
    }







    public function generateQuiz(Request $request)
{
    $topic = $request->input('topic');
    $courseId = $request->input('course_id');

    // Check if course_id is provided
    if (!$courseId) {
        return response()->json(['success' => false, 'message' => 'Course ID is missing.']);
    }

    // Retrieve the course record
        $course = GeneratedCourse::find($courseId);

        if (!$course) {
            \Log::error("Course not found for Course ID: $courseId");
            return response()->json(['success' => false, 'message' => 'Invalid course ID provided.']);
        }

        // Extract details from the course record
        $course_title = $course->course_title;
        $class = $course->class;

        // Log the inputs for verification
        \Log::info("Generating quiz for: Subtopic: $topic, Title: $course_title, Class: $class, Course ID: $courseId");

        // Construct the prompt       

           $prompt = "Create a JSON quiz for the subtopic '{$topic}' under the course title '{$course_title}', designed for students in '{$class}'. The quiz should consist of multiple-choice questions with the following structure: 
        1. Each question must have a clear and concise text under the 'question' key. 
        2. Provide exactly four options for each question under the 'options' key.
        3. Include the correct answer under the 'correctAnswer' key, ensuring it matches one of the provided options.
        4. Follow this exact JSON format:

        {
          \"questions\": [
            {
              \"question\": \"Which of the following is NOT a key environmental factor influencing reproductive success?\",
              \"options\": [
                \"Temperature\",
                \"Food availability\",
                \"Genetic diversity\",
                \"Predator presence\"
              ],
              \"correctAnswer\": \"Genetic diversity\"
            }
          ]
        }

        Generate at least 10 questions for the quiz. Ensure all questions are relevant to the subtopic and appropriate for the specified class level.";


    try {
        $apiKey = env('GOOGLE_API_KEY');
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $prompt,
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->successful()) {
            $responseData = $response->json();

            // Extract the raw JSON content
            $rawJson = data_get($responseData, 'candidates.0.content.parts.0.text');

            if ($rawJson) {
                // Remove code block markers (` ```json ` and ` ``` `)
                $cleanedJson = preg_replace('/^```json\s*|\s*```$/m', '', $rawJson);

                // Validate and decode the JSON to ensure it's properly formatted
                $decodedJson = json_decode($cleanedJson, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // Update or create the quiz in the database
                    \App\Models\Quizzes::updateOrCreate(
                        ['generated_course_id' => $courseId, 'topic_title' => $topic],
                        ['quiz_content' => json_encode($decodedJson)] // Save as properly formatted JSON
                    );

                    Log::info("Quiz generated successfully for Course ID: {$courseId}");

                    return response()->json(['success' => true, 'message' => 'Quiz generated successfully.']);

                } else {
                    Log::error('Invalid JSON format detected after cleaning.', [
                        'error' => json_last_error_msg(),
                        'raw_json' => $rawJson,
                    ]);

                    return response()->json(['success' => false, 'message' => 'Invalid JSON format detected.']);
                }
            } else {
                Log::error('No JSON content generated from API response.');
                return response()->json(['success' => false, 'message' => 'No quiz content found in the API response.']);
            }
        }

        throw new \Exception('Failed to generate quiz.');
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error generating quiz:', ['exception' => $e]);

        return response()->json(['success' => false, 'message' => 'Failed to generate quiz. Please try again.']);
    }
}










public function showContentPage(Request $request)
{
    $subtopic = $request->query('subtopic');
    $courseId = $request->query('course_id');

    // Fetch the content from the database
    $content = SubtopicContent::where('subtopic_title', $subtopic)
                                ->where('generated_course_id', $courseId)
                                ->first();

    if ($content) {
        // Convert Markdown to HTML
        $converter = new CommonMarkConverter();
        $formattedContent = $converter->convertToHtml($content->content);

        return view('content.view', [
            'subtopic' => $subtopic,
            'courseId' => $courseId,
            'content' => $formattedContent
        ]);
    } else {
        return redirect()->back()->with('error', 'Content not found.');
    }
}












//////////////////Below Handles the web chatbot/////////////////////

     public function sendMessage(Request $request)
{
    $message = $request->input('message');

    // Validate the message
    if (empty($message)) {
        return response()->json(['status' => 'error', 'message' => 'Message is required'], 400);
    }

    try {
        // Get the API key from the .env file
        $apiKey = env('GOOGLE_API_KEY');

        Log::info('API Key:', ['key' => $apiKey]);

        // Convert the message to lowercase for the prompt
        $lowercaseMessageDetails = strtolower($message);

        // Create the detailed prompt
        $prompt = "Generate a detailed and educational response to this input: $lowercaseMessageDetails. The response should be thorough, providing an in-depth explanation with examples or equations if relevant. Aim for a length that thoroughly covers the topic and offers valuable insights. However, if the input is not a request for information or a question requiring an in-depth explanation, simply give the most appropriate response.";

        // Make HTTP request to Google Gemini API
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$apiKey}", [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => json_encode($prompt), // Send the custom prompt
                        ],
                    ],
                ],
            ],
        ]);

        // Log the response for debugging purposes
        Log::info('Gemini API Response:', $response->json());

        // Check if the request was successful
        if ($response->successful()) {
            $responseData = $response->json(); // Get the full JSON response

            // Extract the generated text content from the response
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'];

                // Return the generated text as a JSON response
                return response()->json(['status' => 'success', 'response' => $generatedText], 200);
            } else {
                return response()->json(['status' => 'error', 'message' => 'No content generated'], 500);
            }

        } else {
            return response()->json(['status' => 'error', 'message' => 'Failed to generate content. Error: ' . $response->status()], 500);
        }

    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error generating content:', ['exception' => $e]);

        return response()->json(['status' => 'error', 'message' => 'Error generating content. Please try again later.'], 500);
    }
}
/////////////////////end of we chatbot///////////////////////////////


   




}

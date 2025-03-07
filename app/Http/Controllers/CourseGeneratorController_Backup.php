<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    
    public function generateCourse(Request $request)
    {
        // Retrieve inputs from the request
        $mainTopic = $request->input('title');

        // Validate inputs
        if (empty($mainTopic)) {
            return redirect()->back()->withInput()->withErrors(['error' => 'Please fill in all required fields.']);
        }

        // Construct the prompt for Gemini API
        $prompt = "Generate a course outline for the title: {$mainTopic}. Include only subtopics and number the subtopics starting from 1.";

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
                                'text' => json_encode($prompt), // Convert array to JSON string
                            ],
                        ],
                    ],
                ],
            ]);

            // Log the response for debugging purposes
            \Log::info('Gemini API Response:', $response->json());

            // Check if the request was successful
            if ($response->successful()) {
                $responseData = $response->json(); // Get the full JSON response

                // Extract the generated text content from the response
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'];

                    // Extract main topic and subtopics using functions
                    $mainTopic = $this->extractMainTopic($generatedText);
                    $subtopics = $this->extractSubtopics($generatedText);

                    // Prepare an array to store sub-subtopics for each subtopic
                    $subSubtopics = [];
                    foreach ($subtopics as $subtopic) {
                        $subSubtopics[$subtopic] = $this->extractSubSubtopics($generatedText, $subtopic);

                        // Log the extracted sub-subtopics for debugging
                        \Log::info("Sub-subtopics for '{$subtopic}':", $subSubtopics[$subtopic]);
                    }

                    // Pass the generated text content, main topic, subtopics, and sub-subtopics to the Blade view
                    return view('generated-course', [
                        'generatedText' => $generatedText,
                        'mainTopic' => $mainTopic,
                        'subtopics' => $subtopics,
                        'subSubtopics' => $subSubtopics,
                    ]);

                } else {
                    throw new \Exception('No content generated.');
                }

            } else {
                throw new \Exception('Failed to generate course content. Error: ' . $response->status());
            }

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error generating course content:', ['exception' => $e, 'response' => $response->body()]);

            // Handle errors
            return redirect()->back()->withInput()->withErrors(['error' => 'Error generating course content. Please try again later.']);
        }
    }

    // Function to extract main topic from structured text
    private function extractMainTopic($text) {
        // Regular expression pattern to match main topic preceded by ##
        preg_match('/^##\s+(.*?)$/m', $text, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }

    // Function to extract subtopics from structured text
    private function extractSubtopics($text) {
        // Initialize an array to store subtopics
        $subtopics = [];

        // Regular expression to match numbered subtopics with bold titles
        $pattern = '/^\d+\.\s+\*\*(.+?)\*\*$/m';

        // Match all occurrences of subtopics
        preg_match_all($pattern, $text, $matches);

        // Iterate through matched subtopics
        foreach ($matches[1] as $subtopic) {
            $subtopics[] = $subtopic;
        }

        return $subtopics;
    }

    // Function to extract sub-subtopics from structured text for a given subtopic
    private function extractSubSubtopics($text, $subtopic) {
        // Initialize an array to store sub-subtopics
        $subSubtopics = [];

        // Regular expression pattern for extracting sub-subtopics under a given subtopic
        $pattern = '/^\s*[\*\-a-zA-Z]\s+(.+)$/m';

        // Find the start and end position of the current and next subtopic
        $startPosition = strpos($text, "{$subtopic}\n") + strlen($subtopic) + 1; // +1 for newline
        $nextSubtopic = $this->findNextSubtopic($text, $subtopic);

        // If next subtopic found, extract content between current and next subtopic
        if ($nextSubtopic !== false) {
            $endPosition = strpos($text, "{$nextSubtopic}\n", $startPosition);
        } else {
            // If no next subtopic, extract till the end of text
            $endPosition = strlen($text);
        }

        // Extract the content block for the subtopic
        $subtopicContent = substr($text, $startPosition, $endPosition - $startPosition);

        // Log the extracted content for debugging
        \Log::info("Content block for '{$subtopic}':", [$subtopicContent]);

        // Match all occurrences of sub-subtopics within the content block
        preg_match_all($pattern, $subtopicContent, $matches);

        // Log matches for debugging
        \Log::info("Matches for '{$subtopic}':", $matches);

        // Iterate through matched sub-subtopics
        foreach ($matches[1] as $subSubtopic) {
            $subSubtopics[] = $subSubtopic;
        }

        return $subSubtopics;
    }


    // Helper function to find the next subtopic after a given subtopic
    private function findNextSubtopic($text, $currentSubtopic) {
        // Regular expression to find the next subtopic after the current one
        $pattern = "/^(\d+\.\s+\*\*.+?\*\*)/m";
        preg_match($pattern, $text, $matches, PREG_OFFSET_CAPTURE, strpos($text, $currentSubtopic));
        
        return isset($matches[1]) ? $matches[1][0] : false;
    }



    public function generateDetail(Request $request)
{
    $mainTopic = $request->input('mainTopic');
    $subtopic = $request->input('subtopic');
    $subSubtopic = $request->input('subSubtopic');

    // Validate inputs
    if (empty($mainTopic) || empty($subtopic) || empty($subSubtopic)) {
        return redirect()->back()->withErrors(['error' => 'Invalid input']);
    }

    // Construct the prompt for Gemini API
    $prompt = "Generate detailed information for the main topic: {$mainTopic}, subtopic: {$subtopic}, sub-subtopic: {$subSubtopic}.";

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
                            'text' => json_encode($prompt), // Convert array to JSON string
                        ],
                    ],
                ],
            ],
        ]);

        // Log the response for debugging purposes
        \Log::info('Gemini API Response:', $response->json());

        // Check if the request was successful
        if ($response->successful()) {
            $responseData = $response->json(); // Get the full JSON response

            // Extract the generated text content from the response
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'];

                // Log the response for debugging purposes
                \Log::info('Course Details Gemini API Response:', ['generatedDetail' => $generatedText]);

                // Return the course details view with generated content
                // Simulate success
                $success = true;
                $redirect_url = route('coursedetails', [
                    'mainTopic' => $mainTopic,
                    'subtopic' => $subtopic,
                    'subSubtopic' => $subSubtopic,
                    'details' => $generatedText,
                ]);

                // Return the response indicating success and redirect URL
                return response()->json([
                    'success' => $success,
                    'redirect_url' => $redirect_url,
                    'details' => $generatedText, // Include generatedText in the JSON response
                ]);


            } else {
                throw new \Exception('No content generated.');
            }

        } else {
            throw new \Exception('Failed to generate course content. Error: ' . $response->status());
        }

    } catch (\Exception $e) {
        // Log the error for debugging
        \Log::error('Error generating course content:', ['exception' => $e, 'response' => $response->body()]);

        // Handle errors
        return redirect()->back()->withInput()->withErrors(['error' => 'Error generating course content. Please try again later.']);
    }
}





    public function send(Request $request)
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





        public function courseDetails(Request $request)
    {
        $mainTopic = $request->input('mainTopic');
        $subtopic = $request->input('subtopic');
        $subSubtopic = $request->input('subSubtopic');
        $details = $request->input('details');

        $formattedDetails = $this->formatCourseContent($details);

        return view('coursedetails', compact('mainTopic', 'subtopic', 'subSubtopic', 'formattedDetails'));
    }

    function formatCourseContent($details) {
    // Break the content into lines
    $lines = explode("\n", $details);

    // Initialize formatted content
    $formattedContent = "";
    $currentSection = "";

    // Iterate through each line
    foreach ($lines as $line) {
        $trimmedLine = trim($line);

        // Check for titles and subtitles using regular expressions
        if (preg_match('/^## (.+)/', $trimmedLine, $matches)) {
            $formattedContent .= "</div>"; // Close previous section
            $formattedContent .= "<div class='section'>";
            $formattedContent .= "<h2>" . htmlspecialchars($matches[1]) . "</h2>";
        } elseif (preg_match('/^### (.+)/', $trimmedLine, $matches)) {
            $formattedContent .= "<h3>" . htmlspecialchars($matches[1]) . "</h3>";
        } elseif (preg_match('/^\*\*(.+?)\*\*/', $trimmedLine, $matches)) {
            // Replace **text** with <strong>text</strong>
            $formattedLine = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', htmlspecialchars($trimmedLine));
            $formattedContent .= "<p>" . $formattedLine . "</p>";
        } elseif (preg_match('/^\*\s(.+)/', $trimmedLine, $matches)) {
            $formattedContent .= "<ul><li>" . htmlspecialchars($matches[1]) . "</li></ul>";
        } elseif (preg_match('/^(\d+)\.\s(.+)/', $trimmedLine, $matches)) {
            $formattedContent .= "<ol><li>" . htmlspecialchars($matches[2]) . "</li></ol>";
        } else {
            // Add the line to the current section
            $formattedContent .= "<p>" . htmlspecialchars($trimmedLine) . "</p>";
        }
    }

    // Close the last section
    $formattedContent .= "</div>";

    return $formattedContent;
}



}

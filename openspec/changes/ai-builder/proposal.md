## Why
The previous boilerplate-copying approach lacked the true feeling of a modern System Builder. To provide an authentic Developer Experience, projects should be generated from scratch using Composer so that they always receive the latest Laravel version. Additionally, to make it a true "AI Code Builder", we must integrate Google's Gemini AI to parse user intent via a chat interface and edit the project's source code automatically.

## What Changes
- Replace boilerplate copy mechanism with a real-time `composer create-project` execution process.
- Implement a terminal-like animated UI during the project generation process to show progress.
- Integrate the Gemini API to take user prompts from a new Chat Interface inside the Web Code Viewer.
- Incorporate a Chat Interface inside the Web Code Viewer to interact with Gemini.
- Display AI-generated code edits on the Viewer's Code Editor.

## Capabilities

### Modified Capabilities
- `project-scaffolder`: Update the backend logic from copying directories to executing `composer create-project` and stream the output to the frontend (Terminal UI).
- `web-code-viewer`: Expand the viewer UI layout to include an AI Chat panel and update editor to reflect changes.

### New Capabilities
- `ai-code-editor`: Integrate the Gemini API to process prompts from the chat interface, analyze file contexts, and apply code modifications directly to the generated project files.

## Impact
- Adds an external dependency (Gemini API) requiring `GEMINI_API_KEY` in environment variables.
- Requires building a streaming mechanism (SSE or AJAX polling) to stream Composer output to the UI.
- The web code viewer UI will need adjustments to accommodate the chat layout side-by-side or as a panel.

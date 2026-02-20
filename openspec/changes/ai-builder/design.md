## Context
The system currently copies an existing Laravel boilerplate. The new requirement makes it generate a fresh Laravel installation on-the-fly via `composer create-project` while streaming the CLI output to the browser via a terminal-like experience. Once the project is created and the viewer opens, there must be a chat interface to communicate with Gemini AI. When a user asks a question, the AI should be able to read/write files and update the UI.

## Goals / Non-Goals

**Goals:**
- Replace Boilerplate with true `composer create-project` execution via Symfony Process.
- Show live progress in the frontend using SSE (Server Sent Events) or API polling.
- Connect the frontend chat to a backend controller that interfaces with Gemini API.
- Parse structured outputs from Gemini to apply UI changes or file modifications.
- Retain the `.env` templating process but execute it right after composer completes.

**Non-Goals:**
- Fully-featured terminal access for users. The terminal UI is merely a visual readout.
- Building a complex collaborative collaborative editor (only the AI agents write data).

## Decisions
1. **Gemini Integration API**: To perform file editing based on user prompts, we will use Laravel's `Http` client to communicate with the Gemini REST API (`https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent`). We will inject the API key directly into `.env`.
2. **Prompt Structuring**: To let Gemini edit files, the system prompt must explicitly request JSON containing `{ path: "...", content: "..." }` or similar instruction blocks.
3. **Generation Streaming Status**: Since `create-project` takes minutes, we'll run it synchronously via a long-polling AJAX request or an EventStream that fetches lines buffered in a local log file, which prevents PHP max execution timeouts from silently killing the request. We will record the output to a unique log file and the frontend will fetch that log repeatedly.

## Risks / Trade-offs
- **Timeout**: `composer create-project` can take 2-3 minutes. PHP max execution time must be disabled (`set_time_limit(0)`).
- **Gemini Hallucinations**: Gemini might output markdown inside code or omit necessary pieces. We will prompt it strictly.

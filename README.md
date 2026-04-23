# Random Moving Squares

This project is a small Pygame application that opens a window with 10 colorful squares moving around the screen and bouncing off the edges.

## What the app uses

- Python 3.12+ recommended
- `pygame`

## How to run it

1. Create and activate a Python virtual environment:

```bash
python -m venv .venv
```

On Windows PowerShell:

```bash
.\.venv\Scripts\Activate.ps1
```

If execution policy blocks activation, run commands with the interpreter directly:

```bash
.\.venv\Scripts\python.exe -m pip install -r requirements.txt
.\.venv\Scripts\python.exe main.py
```

2. Install the project dependency:

```bash
pip install -r requirements.txt
```

3. Start the app:

```bash
python main.py
```

## What is in the code

- `main.py` is now a small entrypoint only.
- `app.py` runs the game loop and orchestrates UI + logic.
- `config.py` contains app constants (window size, FPS, colors, etc.).
- `models.py` contains data models (`Square`).
- `logic.py` contains pure game logic (movement updates and collision handling).
- `ui.py` contains pygame-only UI behavior (window/events/drawing).

This structure enforces separation of concerns:

- UI concerns stay in `ui.py`.
- Simulation logic stays in `logic.py`.
- Data/state shape stays in `models.py`.
- Bootstrapping and flow control stay in `app.py` and `main.py`.

## What has happened in this repository

This workspace was developed through a series of prompts and updates:

- The project began as a request for a simple Python animation.
- The implementation was changed from Tkinter to Pygame.
- The app was refined into a random moving squares animation.
- A code explorer learning page was generated in `docs/code_explorer.html`.
- A journal workflow was activated so changes are tracked in `JOURNAL.md`.
- This README was added to explain the current state of the project and how to run it.

## Notes

- If you see `No module named pygame`, run the install command above inside the environment you are using to launch the app.
- The project currently depends only on `pygame` beyond the Python standard library.
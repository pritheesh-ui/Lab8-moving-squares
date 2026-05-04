# Overview

This project is a Pygame simulation where squares move, chase/flee based on size, bounce on walls, fade with lifetime, and collide with each other. The structure is already good for a beginner project because responsibilities are split across files (`app.py`, `logic.py`, `ui.py`, `models.py`, `config.py`).

The main opportunity is to make the code easier to read and maintain by breaking one large function (`update_squares`) into smaller helper functions, improving a few names, and reducing repeated calculations. The goal is to keep behavior exactly the same while making the logic easier for first-year students to debug and extend.

# Refactoring Goals

1. Improve readability of complex logic, especially in `logic.py`.
2. Reduce duplication in collision and center-point calculations.
3. Make function and variable names more descriptive for learning.
4. Keep all game behavior unchanged.
5. Prepare the code for easier testing and debugging in small steps.
6. Ensure the final refactored code includes concise inline comments that explain what changed and why.

# Step-by-Step Refactoring Plan

## Step 1: Add short docstrings to top-level functions

What to do:
- Add one short docstring to each public function in `app.py`, `logic.py`, and `ui.py`.
- Keep docstrings simple: one sentence about purpose and one sentence about important input/output behavior.

Why this helps:
- Beginners can understand each function quickly without reading all implementation details.
- It improves navigation in editors and makes code self-explaining.

Inline comment instruction for final code:
- Add one concise inline comment before any function where behavior was clarified, for example: explain that a docstring was added to make the function contract clear.

Optional before/after snippet:

Before:
```python
def handle_rebirth(squares: list[Square], width: int, height: int) -> None:
```

After:
```python
def handle_rebirth(squares: list[Square], width: int, height: int) -> None:
    """Replace expired squares with fresh random squares."""
```

## Step 2: Extract repeated center-point math into a helper

What to do:
- In `logic.py`, create a helper function like `get_center(square: Square) -> tuple[float, float]`.
- Replace repeated expressions such as `a.x + a.size / 2` and `a.y + a.size / 2` with this helper.

Why this helps:
- Reduces repeated arithmetic and visual noise in `update_squares`.
- Makes intent clearer: the code is asking for a center point, not re-deriving coordinates each time.

Inline comment instruction for final code:
- Add an inline comment near the helper call explaining that center-point math was centralized to avoid duplication and mistakes.

Optional before/after snippet:

Before:
```python
cx_a, cy_a = a.x + a.size / 2, a.y + a.size / 2
```

After:
```python
cx_a, cy_a = get_center(a)
```

## Step 3: Split predator/prey search from movement update

What to do:
- Extract the inner neighbor scan in `update_squares` into a helper function, for example:
  - `find_closest_neighbors(current: Square, squares: list[Square]) -> tuple[Square | None, float, Square | None, float]`
- Keep all logic identical (same comparisons and distance calculations).

Why this helps:
- `update_squares` currently does multiple jobs: neighbor analysis, state decision, acceleration, speed clamping, and wall handling.
- Splitting one job out makes each part easier to test and explain in class.

Inline comment instruction for final code:
- Add an inline comment where the helper is called explaining that neighbor analysis was extracted to reduce cognitive load in the main loop.

## Step 4: Split state decision from acceleration application

What to do:
- Extract the state-selection block (`FLEE`/`CHASE`/`WANDER`) into a helper function, for example:
  - `choose_state_and_target(...) -> tuple[State, Square | None, float]`
- Keep panic/calm threshold logic exactly the same.

Why this helps:
- Makes state transitions explicit and easier to reason about.
- Helps beginners understand finite-state behavior as a separate concept.

Inline comment instruction for final code:
- Add an inline comment at the helper call explaining that state logic was isolated so behavior rules are easier to verify.

## Step 5: Extract boundary bounce handling

What to do:
- Move wall-collision and clamping logic into a helper function, for example:
  - `apply_wall_bounce(square: Square, width: int, height: int) -> None`
- Keep bounce behavior unchanged (`vx`/`vy` inversion and clamping).

Why this helps:
- Wall handling is reusable and conceptually separate from steering behavior.
- Reduces `update_squares` length and improves readability.

Inline comment instruction for final code:
- Add an inline comment in the helper explaining that clamping after bounce prevents leaving the visible screen.

## Step 6: Improve naming for teaching clarity

What to do:
- Rename short or unclear variable names in `logic.py` where safe, for example:
  - `a` -> `square`
  - `b` -> `other_square`
  - `dist` -> `distance`
- Keep loop behavior unchanged.

Why this helps:
- Beginner readers can track meaning without mentally decoding single-letter names.
- Better names reduce debugging mistakes.

Inline comment instruction for final code:
- Add concise inline comments where naming updates happened to explain that names were expanded for readability without changing behavior.

## Step 7: Simplify collision response readability

What to do:
- In `resolve_collisions_once`, extract repeated center comparisons and push logic into tiny helpers if needed (for example, axis-specific push/swap operations).
- Keep collision results identical.

Why this helps:
- Collision code is mathematically dense; small helpers make each rule easier to validate.
- Supports step-by-step debugging when collision behavior looks wrong.

Inline comment instruction for final code:
- Add inline comments at key decision points (`overlap_x < overlap_y`, velocity swap) explaining what changed and why the structure is easier to maintain.

## Step 8: Add lightweight defensive checks where inputs are assumed valid

What to do:
- Add small guard clauses in helper functions where mathematically relevant (for example, avoid normalizing near-zero distance, which the code already does).
- Keep runtime behavior equivalent in normal cases.

Why this helps:
- Makes assumptions explicit and teaches defensive programming.
- Prevents accidental future regressions when code is extended.

Inline comment instruction for final code:
- Add concise comments that explain what edge case is being guarded and why it matters for correctness.

## Step 9: Keep app loop readable with tiny lifecycle helpers

What to do:
- In `app.py`, optionally extract grouped frame tasks into tiny wrappers such as:
  - `update_frame_state(...)`
  - `render_frame(...)`
- Keep call order exactly the same: quit check -> dt -> updates -> draw -> fps text -> flip.

Why this helps:
- Reinforces game-loop structure and separation of concerns.
- Makes future features easier to add without growing one long function.

Inline comment instruction for final code:
- Add inline comments noting that helper extraction preserves existing order while improving maintainability.

## Step 10: Refactor incrementally with behavior checks after each step

What to do:
- Apply one step at a time.
- Run the game after each step to ensure visuals and behavior stay the same.
- If behavior changes, revert that specific step and re-apply carefully.

Why this helps:
- Teaches safe refactoring practices.
- Prevents multiple simultaneous changes from hiding the source of a bug.

Inline comment instruction for final code:
- Add short comments only for meaningful changes, not every line, so the final code stays readable.

# Final Output Requirements (Mandatory)

When this plan is executed, the output MUST:

1. Contain only the refactored code.
2. Include concise, beginner-friendly inline comments that explain:
   - What changed
   - Why it improves readability, maintainability, or correctness
   - Relevant programming concepts (for example: helper functions, separation of concerns, state transitions, defensive checks)
3. Preserve the original behavior and structure as much as possible.
4. Avoid advanced patterns or heavy abstractions.

# Key Concepts for Students

1. Separation of concerns: keep movement, state decisions, collisions, and drawing in clear blocks.
2. Refactoring vs rewriting: improve structure without changing behavior.
3. Helper functions: reduce duplication and make intent clearer.
4. State-driven behavior: `WANDER`, `CHASE`, and `FLEE` model behavior cleanly.
5. Defensive programming: protect calculations from invalid or edge-case inputs.
6. Incremental testing: verify one change at a time to localize issues.

# Safety Notes

1. Test after each small refactor step by running the game loop and visually checking movement, fading, collision response, and quit behavior.
2. Do not change constants or tuning values (`PANIC_MULT`, `CALM_MULT`, speed formulas) during structure-only refactors.
3. Keep function signatures stable where possible to avoid breaking imports across modules.
4. Avoid combining many renames and logic edits in one commit; separate structural changes from behavior-sensitive changes.
5. If a helper extraction changes behavior unexpectedly, roll back that one step and compare old/new code paths with print-debugging.

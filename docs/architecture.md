# Project Architecture

This project is a small Pygame simulation built around a narrow entrypoint and four focused modules. `main.py` starts the app, `app.py` owns the loop, `logic.py` updates state, `ui.py` handles pygame-facing work, `models.py` defines the data shape, and `config.py` centralizes constants.

## Module Dependency Graph

```mermaid
flowchart TD
    main["main.py"] --> app["app.py"]
    app --> config["config.py"]
    app --> logic["logic.py"]
    app --> ui["ui.py"]
    logic --> config
    logic --> models["models.py"]
    ui --> models
    app --> pygame["pygame"]
    logic --> pygame
    ui --> pygame
```

`main.py` is only a bootstrapper. The game loop and frame orchestration live in `app.py`, while `logic.py` and `ui.py` keep simulation behavior and rendering behavior separated.

## Runtime Flow

```mermaid
flowchart TD
    start["Program start"] --> entry["main.py calls app.run()"]
    entry --> init["Initialize pygame window, clock, font, and squares"]
    init --> loop["Main game loop"]
    loop --> quitcheck["Poll events with should_quit()"]
    quitcheck -->|"Quit event"| shutdown["Exit loop and call pygame.quit()"]
    quitcheck -->|"No quit event"| timestep["Advance time with clock.tick(FPS)"]
    timestep --> update["Update movement, lifetimes, rebirth, and collisions"]
    update --> draw["Clear the screen and draw all squares"]
    draw --> present["Render FPS text and flip the display"]
    present --> loop
```

Each frame follows the same order: read input, advance the simulation, render the scene, and present the result.

## Function-Level Call Graph

```mermaid
flowchart TD
    main_run["main.py: run()"] --> app_run["app.py: run()"]
    app_run --> create_screen["ui.py: create_screen()"]
    create_screen --> pygame_init["pygame.init()"]
    app_run --> clock["pygame.time.Clock()"]
    app_run --> font_init["pygame.font.init()"]
    app_run --> create_squares["logic.py: create_squares()"]
    create_squares --> create_random_square["logic.py: create_random_square()"]
    app_run --> should_quit["ui.py: should_quit()"]
    should_quit --> event_get["pygame.event.get()"]
    app_run --> update_squares["logic.py: update_squares()"]
    app_run --> update_lifetimes["logic.py: update_lifetimes()"]
    app_run --> handle_rebirth["logic.py: handle_rebirth()"]
    handle_rebirth --> create_random_square
    app_run --> resolve_collisions_once["logic.py: resolve_collisions_once()"]
    resolve_collisions_once --> rects["pygame.Rect and colliderect()"]
    app_run --> draw_squares["ui.py: draw_squares()"]
    draw_squares --> fill["screen.fill()"]
    draw_squares --> draw_rect["pygame.draw.rect()"]
    app_run --> flip["pygame.display.flip()"]
```

The central function is `app.run()`. It composes small helpers instead of embedding all behavior in one place, which keeps the code easy to follow.

## Primary Execution Sequence

```mermaid
sequenceDiagram
    participant M as "main.py"
    participant A as "app.py run()"
    participant UI as "ui.py"
    participant L as "logic.py"
    participant P as "pygame"

    M->>A: run()
    A->>UI: create_screen(width, height, title)
    UI->>P: init()
    UI->>P: display.set_mode(...)
    UI->>P: display.set_caption(...)
    A->>P: time.Clock()
    A->>P: font.init()
    A->>L: create_squares(NUM_SQUARES, WIDTH, HEIGHT)

    loop "Each frame"
        A->>UI: should_quit()
        UI->>P: event.get()
        alt "Quit event found"
            UI-->>A: True
            A->>P: quit()
            A-->>M: return
        else "No quit event"
            UI-->>A: False
            A->>P: clock.tick(FPS)
            A->>L: update_squares(squares, WIDTH, HEIGHT, dt)
            A->>L: update_lifetimes(squares, dt)
            A->>L: handle_rebirth(squares, WIDTH, HEIGHT)
            A->>L: resolve_collisions_once(squares, colliding_pairs)
            L-->>A: updated collision set
            A->>UI: draw_squares(screen, squares, BACKGROUND_COLOR)
            UI->>P: Surface and draw.rect calls
            A->>P: display.flip()
        end
    end
```

The main branch point is quitting. If no quit event is present, the loop advances one frame and repeats.

## Assumptions

- The architecture is inferred from the current source files in this workspace.
- There are no hidden runtime modules beyond the files listed above.
- The docs describe the current program structure, not a future refactor plan.

import pygame

from config import BACKGROUND_COLOR, FPS, HEIGHT, NUM_SQUARES, WIDTH
from logic import (
    create_squares,
    handle_rebirth,
    resolve_collisions_once,
    update_lifetimes,
    update_squares,
)
from ui import create_screen, draw_squares, should_quit


FPS_TEXT_COLOR = (255, 255, 255)
FPS_TEXT_POSITION = (10, 10)

def run() -> None:
    screen = create_screen(WIDTH, HEIGHT, "Random Moving Squares Part III")
    clock = pygame.time.Clock()

    pygame.font.init()
    font = pygame.font.SysFont(None, 24)

    squares = create_squares(NUM_SQUARES, WIDTH, HEIGHT)
    colliding_pairs: set[tuple[int, int]] = set()

    running = True
    while running:
        if should_quit():
            running = False
            continue

        dt = clock.tick(FPS) / 1000.0

        update_squares(squares, WIDTH, HEIGHT, dt)

        update_lifetimes(squares, dt)
        handle_rebirth(squares, WIDTH, HEIGHT)

        colliding_pairs = resolve_collisions_once(
            squares,
            colliding_pairs,
        )

        draw_squares(screen, squares, BACKGROUND_COLOR)

        fps_text = font.render(f"FPS: {clock.get_fps():.1f}", True, FPS_TEXT_COLOR)
        screen.blit(fps_text, FPS_TEXT_POSITION)

        pygame.display.flip()

    pygame.quit()
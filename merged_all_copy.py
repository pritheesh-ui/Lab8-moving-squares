# ==============================
# Copied from: config.py
# ==============================
WIDTH = 800
HEIGHT = 600
NUM_SQUARES = 10
FPS = 60
BACKGROUND_COLOR = (20, 20, 28)
MIN_LIFETIME = 30.0
MAX_LIFETIME = 180.0


# ==============================
# Copied from: models.py
# ==============================
from dataclasses import dataclass
from enum import Enum, auto


class State(Enum):
    WANDER = auto()
    CHASE = auto()
    FLEE = auto()


Color = tuple[int, int, int]


@dataclass
class Square:
    x: float
    y: float
    vx: float
    vy: float
    color: Color
    size: int
    max_speed: float
    max_accel: float
    lifetime: float = 0.0
    max_lifetime: float = 0.0
    alpha: int = 255
    state: State = State.WANDER


# ==============================
# Copied from: ui.py
# ==============================
import pygame


def create_screen(width: int, height: int, title: str) -> pygame.Surface:
    pygame.init()
    screen = pygame.display.set_mode((width, height))
    pygame.display.set_caption(title)
    return screen


def should_quit() -> bool:
    for event in pygame.event.get():
        if event.type == pygame.QUIT:
            return True
    return False


def draw_squares(
    screen: pygame.Surface,
    squares: list[Square],
    background_color: Color,
) -> None:
    screen.fill(background_color)

    for s in squares:
        temp_surface = pygame.Surface((s.size, s.size), pygame.SRCALPHA)
        pygame.draw.rect(temp_surface, s.color + (s.alpha,), (0, 0, s.size, s.size))
        screen.blit(temp_surface, (int(s.x), int(s.y)))


# ==============================
# Copied from: logic.py
# ==============================
import math
import random

PANIC_MULT = 4.0
CALM_MULT = 6.0


def create_random_square(width: int, height: int, size: int) -> Square:
    max_speed = 5000.0 / size
    max_accel = 8000.0 / size

    return Square(
        x=random.uniform(0, width - size),
        y=random.uniform(0, height - size),
        vx=random.uniform(-max_speed, max_speed),
        vy=random.uniform(-max_speed, max_speed),
        color=(random.randint(80, 255), random.randint(80, 255), random.randint(80, 255)),
        size=size, 
        max_speed=max_speed,
        max_accel=max_accel,
        lifetime=0.0,
        max_lifetime=random.uniform(MIN_LIFETIME, MAX_LIFETIME),
    )


def create_squares( width: int, height: int,) -> list[Square]:
    squares = []
    
    mix = [
        (5, 25),  # 5 squares of 25px
        (10, 10), # 10 squares of 10px
        (30, 4)   # 30 squares of 4px
    ]
    
    for count, size in mix:
        for _ in range(count):
            squares.append(create_random_square(width, height, size))
            
    return squares


def update_lifetimes(squares: list[Square], dt: float) -> None:
    for square in squares:
        square.lifetime += dt
        remaining_ratio = max(0.0, 1.0 - (square.lifetime / square.max_lifetime))
        square.alpha = int(remaining_ratio * 255)


def handle_rebirth(squares: list[Square], width: int, height: int) -> None:
    for idx, square in enumerate(squares):
        if square.lifetime >= square.max_lifetime:
            squares[idx] = create_random_square(width, height, square.size)


def update_squares(squares: list[Square], width: int, height: int, dt: float) -> None:
    for a in squares:
        cx_a, cy_a = a.x + a.size / 2, a.y + a.size / 2
        panic_radius = a.size * PANIC_MULT
        calm_radius = a.size * CALM_MULT

        closest_predator = None
        closest_prey = None
        min_dist_pred = float("inf")
        min_dist_prey = float("inf")

        for b in squares:
            if a is b:
                continue

            cx_b, cy_b = b.x + b.size / 2, b.y + b.size / 2
            dist = math.hypot(cx_a - cx_b, cy_a - cy_b)

            if b.size > a.size:
                if dist < min_dist_pred:
                    min_dist_pred = dist
                    closest_predator = b
            elif b.size < a.size:
                if dist < min_dist_prey:
                    min_dist_prey = dist
                    closest_prey = b

        target = None
        current_dist = 0.0

        if closest_predator and min_dist_pred < panic_radius:
            a.state = State.FLEE
            target = closest_predator
            current_dist = min_dist_pred
        elif closest_prey and min_dist_prey < panic_radius:
            a.state = State.CHASE
            target = closest_prey
            current_dist = min_dist_prey
        else:
            if a.state == State.FLEE and closest_predator and min_dist_pred < calm_radius:
                target = closest_predator
                current_dist = min_dist_pred
            elif a.state == State.CHASE and closest_prey and min_dist_prey < calm_radius:
                target = closest_prey
                current_dist = min_dist_prey
            else:
                a.state = State.WANDER

        ax, ay = 0.0, 0.0

        if a.state != State.WANDER and target and current_dist > 1e-6:
            dx = cx_a - (target.x + target.size / 2)
            dy = cy_a - (target.y + target.size / 2)
            ux, uy = dx / current_dist, dy / current_dist

            if a.state == State.FLEE:
                ax, ay = ux * a.max_accel, uy * a.max_accel
            elif a.state == State.CHASE:
                ax, ay = -ux * a.max_accel, -uy * a.max_accel

            noise_factor = 0.05
        else:
            noise_factor = 0.3

        ax += random.uniform(-a.max_accel, a.max_accel) * noise_factor
        ay += random.uniform(-a.max_accel, a.max_accel) * noise_factor

        a.vx += ax * dt
        a.vy += ay * dt

        speed = math.hypot(a.vx, a.vy)
        if speed > a.max_speed:
            a.vx = (a.vx / speed) * a.max_speed
            a.vy = (a.vy / speed) * a.max_speed

        a.x += a.vx * dt
        a.y += a.vy * dt

        # Horizontal Wrap
        if a.x < -a.size:
            a.x = width
        elif a.x > width:
            a.x = -a.size

        # Vertical Wrap (Now aligned with Horizontal Wrap)
        if a.y < -a.size:
            a.y = height
        elif a.y > height:
            a.y = -a.size


def resolve_collisions_once(
    squares: list[Square],
    colliding_pairs: set[tuple[int, int]],
) -> set[tuple[int, int]]:
    new_colliding_pairs: set[tuple[int, int]] = set()

    for i, a in enumerate(squares):
        for j, b in enumerate(squares[i + 1 :], i + 1):
            rect_a = pygame.Rect(int(a.x), int(a.y), a.size, a.size)
            rect_b = pygame.Rect(int(b.x), int(b.y), b.size, b.size)

            if not rect_a.colliderect(rect_b):
                continue

            pair = (i, j)
            new_colliding_pairs.add(pair)
            if pair not in colliding_pairs:
                a.color, b.color = b.color, a.color

            overlap_x = min(a.x + a.size, b.x + b.size) - max(a.x, b.x)
            overlap_y = min(a.y + a.size, b.y + b.size) - max(a.y, b.y)
            if overlap_x <= 0 or overlap_y <= 0:
                continue

            if overlap_x < overlap_y:
                push = overlap_x / 2.0
                if a.x + a.size / 2.0 < b.x + b.size / 2.0:
                    a.x -= push
                    b.x += push
                else:
                    a.x += push
                    b.x -= push
                a.vx, b.vx = b.vx, a.vx
            else:
                push = overlap_y / 2.0
                if a.y + a.size / 2.0 < b.y + b.size / 2.0:
                    a.y -= push
                    b.y += push
                else:
                    a.y += push
                    b.y -= push
                a.vy, b.vy = b.vy, a.vy

    return new_colliding_pairs


# ==============================
# Copied from: app.py
# ==============================
FPS_TEXT_COLOR = (255, 255, 255)
FPS_TEXT_POSITION = (10, 10)


def run() -> None:
    screen = create_screen(WIDTH, HEIGHT, "Random Moving Squares Part III")
    clock = pygame.time.Clock()

    pygame.font.init()
    font = pygame.font.SysFont(None, 24)

    squares = create_squares( WIDTH, HEIGHT)
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


# ==============================
# Copied from: main.py
# ==============================
if __name__ == "__main__":
    run()

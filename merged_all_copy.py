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
from dataclasses import dataclass, field
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
    trail: list[tuple[float, float]] = field(default_factory=list)
    target_size: float = 0.0     
    is_growing: bool = False      
    def __post_init__(self):
        self.target_size = float(self.size)
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

        for i in range(len(s.trail) - 1):
            p1 = s.trail[i]
            p2 = s.trail[i+1]
            dist = math.hypot(p1[0] - p2[0], p1[1] - p2[1])
            if dist < WIDTH / 2: 
                pygame.draw.line(screen, s.color, p1, p2, 2)

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
    GROWTH_DURATION = 0.5  
    
    for s in squares:
        if s.is_growing:
            growth_step = (s.target_size - s.size) * (dt / GROWTH_DURATION)
            s.size += int(growth_step) if abs(growth_step) >= 1 else (1 if growth_step > 0 else 0)
            
            s.max_speed = 5000.0 / s.size
            s.max_accel = 8000.0 / s.size
            
            if s.size >= int(s.target_size):
                s.size = int(s.target_size)
                s.is_growing = False

        s.x += s.vx * dt
        s.y += s.vy * dt
        
        if s.x > width: s.x = -s.size
        elif s.x < -s.size: s.x = width
        if s.y > height: s.y = -s.size
        elif s.y < -s.size: s.y = height

        s.trail.append((s.x + s.size / 2, s.y + s.size / 2))
        if len(s.trail) > 30: s.trail.pop(0)
def resolve_collisions_once(
    squares: list[Square],
    colliding_pairs: set[tuple[int, int]],
) -> set[tuple[int, int]]:
    new_colliding_pairs: set[tuple[int, int]] = set()

    for i, a in enumerate(squares):
        for j, b in enumerate(squares[i + 1 :], i + 1):
            if not check_collision(a, b):
                continue

            pair = (i, j)
            new_colliding_pairs.add(pair)

            if pair not in colliding_pairs:
                if a.size > b.size:
                    bigger, smaller = a, b
                elif b.size > a.size:
                    bigger, smaller = b, a
                else:
                    a.color, b.color = b.color, a.color
                    continue
                smaller.lifetime = smaller.max_lifetime 
                if bigger.size < 50:
                    bigger.size += smaller.size
                    # Ensure it doesn't accidentally exceed 50
                    if bigger.size > 50:
                        bigger.size = 50
                bigger.max_speed = 5000.0 / bigger.size 
                bigger.max_accel = 8000.0 / bigger.size
                if bigger.size < 50:
                 bigger.target_size = min(50.0, bigger.size + smaller.size)
                 bigger.is_growing = True
    return new_colliding_pairs
 

  
def check_collision(a: Square, b: Square) -> bool:
    rect_a = pygame.Rect(int(a.x), int(a.y), a.size, a.size)
    rect_b = pygame.Rect(int(b.x), int(b.y), b.size, b.size)
    return rect_a.colliderect(rect_b)

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

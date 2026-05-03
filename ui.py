import pygame

from models import Color, Square


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
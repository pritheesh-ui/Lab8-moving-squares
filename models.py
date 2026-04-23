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
# Lab 10: Pygame Dev Log - Life Span stuff

## Phase 1: Gettin started
Setup the basic squares class. Just had them moving around the screen using x and y co-ords. Kept it simple with just bouncing off walls. Also i couldnt run the pygame with my version and i have to get the older version(3.12.10) and download it and run it

## Phase 2: Collisoin
Got the collision logic working. Added the rect stuff so they know when they hit each other and swapping colors on hit.

## Phase 3: Behavior (Chase / Flee)
This part was the hardest. Making them run away from bigger squares was tricky to get right with the vectors. Took me a while to figure out the subtraction logic for the flee/chase state, but it runs pretty smoothly now. 

## Phase 4: Life Span + Rebirth 
Added the `lifetime` and `max_lifetime` attributes to the dataclass. Used `dt` so they age consistently even if the framerate dips. 

The rebirth part was easy enough just replaced the object at the index when they 'die'. To make it look less weird, I added a transparancy effect. It makes the death look like a feature instead of just popping out of existence.

## Challenges
Honestly the hardest part was downloading an older version of python and running it and also the flee and chase feature

Also i have done it in seperate files because in the end of lab 7 there was a instruction in the slides to ask the Ai to seperate the code in different files you can check the journal there i have asked the copilot to breake my code and move it into different files
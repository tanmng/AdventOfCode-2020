N = 20201227;   # This is a prime
m = 7;
CARD_PUB = 15628416;  # This is 7^e1 mod N
DOOR_PUB = 11161639;  # This is 7^e2 mod N
a = 1;
while True:
    if pow(7, a, N) == CARD_PUB:
        break;
    a += 1

print(pow(DOOR_PUB, a, N))

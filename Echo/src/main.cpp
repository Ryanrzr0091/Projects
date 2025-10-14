#include <Arduino.h>
#include <algorithm>

// ---- Pin map ----
constexpr uint8_t TRIG_A = 18;  // Left sensor TRIG
constexpr uint8_t ECHO_A = 19;  // Left sensor ECHO (⚠ level-shift if you can)
constexpr uint8_t TRIG_B = 23;  // Right sensor TRIG
constexpr uint8_t ECHO_B = 17;   // Right sensor ECHO (⚠ level-shift if you can)

// ---- Constants ----
// Speed of sound ~343 m/s at 20°C => 0.0343 cm/µs
constexpr float CM_PER_US = 0.0343f;
// Max measurable distance (in microseconds of echo time).
// ~30,000 µs covers ~5 m round-trip; adjust if you need more.
constexpr uint32_t ECHO_TIMEOUT_US = 30000;
// Quiet time between firings so echoes from one don’t pollute the other.
constexpr uint16_t INTER_SENSOR_DELAY_MS = 60;
// Quiet time between full cycles (optional smoothing)
constexpr uint16_t CYCLE_DELAY_MS = 60;

static float measure_cm(uint8_t trig, uint8_t echo) {
  // Ensure clean start
  digitalWrite(trig, LOW);
  delayMicroseconds(3);

  // 10 µs trigger pulse
  digitalWrite(trig, HIGH);
  delayMicroseconds(10);
  digitalWrite(trig, LOW);

  // Time the incoming echo pulse
  unsigned long us = pulseIn(echo, HIGH, ECHO_TIMEOUT_US);
  if (us == 0) return NAN;  // timeout / out of range

  // Convert round-trip time to one-way distance
  return (us * CM_PER_US) / 2.0f;
}

// Simple median of 3 to tame spurious spikes
static float median3(float a, float b, float c) {
  if (isnan(a) || isnan(b) || isnan(c)) {
    // If any are NAN, just prefer a real value if available
    if (!isnan(a)) return a;
    if (!isnan(b)) return b;
    if (!isnan(c)) return c;
    return NAN;
  }
  if (a > b) std::swap(a, b);
  if (b > c) std::swap(b, c);
  if (a > b) std::swap(a, b);
  return b; // middle value
}

void setup() {
  Serial.begin(115200);
  Serial.println("\nDual HC-SR04 (staggered) starting...");
  pinMode(TRIG_A, OUTPUT);
  pinMode(ECHO_A, INPUT);
  pinMode(TRIG_B, OUTPUT);
  pinMode(ECHO_B, INPUT);

  // Idle low on TRIG lines
  digitalWrite(TRIG_A, LOW);
  digitalWrite(TRIG_B, LOW);
}

void loop() {
  // --- LEFT sensor (A) ---
  float a1 = measure_cm(TRIG_A, ECHO_A);
  delay(10);
  float a2 = measure_cm(TRIG_A, ECHO_A);
  delay(10);
  float a3 = measure_cm(TRIG_A, ECHO_A);
  float left_cm = median3(a1, a2, a3);

  // Let echoes die before pinging the other sensor
  delay(INTER_SENSOR_DELAY_MS);

  // --- RIGHT sensor (B) ---
  float b1 = measure_cm(TRIG_B, ECHO_B);
  delay(10);
  float b2 = measure_cm(TRIG_B, ECHO_B);
  delay(10);
  float b3 = measure_cm(TRIG_B, ECHO_B);
  float right_cm = median3(b1, b2, b3);

  // Print nicely
  Serial.print("Left: ");
  if (isnan(left_cm)) Serial.print("—");
  else Serial.print(left_cm, 1);

  Serial.print(" cm    |    Right: ");
  if (isnan(right_cm)) Serial.print("—");
  else Serial.print(right_cm, 1);
  Serial.println(" cm");

  delay(CYCLE_DELAY_MS);
}
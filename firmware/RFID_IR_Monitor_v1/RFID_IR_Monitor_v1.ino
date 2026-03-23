/*
 * ═══════════════════════════════════════════════════════════════
 *  ABG Prime Builders Supplies - RFID + IR Sensor + Buzzer System
 *  Arduino Uno R3 + MFRC522 + IR Obstacle Sensor + Passive Buzzer
 *
 *  FEATURES:
 *   • RFID tag read / write via MFRC522 (command-driven)
 *   • IR obstacle detection (non-blocking, state-change output)
 *   • Audible buzzer alarm on IR motion detection (Hardware switch controlled)
 *
 *  SUPPORTED TAG TYPES (auto-detected):
 *   • MIFARE Classic 1K / 4K  — uses key-based sector auth
 *   • MIFARE Ultralight        — page-based, no auth
 *   • NTAG213 / NTAG215 / NTAG216 — page-based, no auth
 *
 *  SERIAL COMMANDS (9600 baud):
 *   PING          → responds PONG
 *   READ          → waits for tag, responds CARD:<item_code>
 *   WRITE:<code>  → waits for tag, writes item code (max 16 chars)
 *   BUZZ_TEST     → sounds the buzzer for testing
 *
 *  SERIAL OUTPUT (autonomous):
 *   MOTION        → IR obstacle detected (buzzer sounds)
 *   CLEAR         → IR path is clear (on state change)
 *   TAG_TYPE:<t>  → tag type printed after card is detected
 *
 *  WIRING (MFRC522 → Arduino Uno R3):
 *   SDA  → 10  |  SCK  → 13  |  MOSI → 11  |  MISO → 12
 *   RST  →  9  |  3.3V → 3.3V (NOT 5V!)     |  GND  → GND
 *
 *  IR SENSOR → Arduino Uno R3:
 *   OUT  →  2  (active-low: LOW = obstacle detected)
 *
 *  BUZZER → Arduino Uno R3:
 *   SIG  →  3  (passive buzzer, driven via tone())
 *   VCC  →  5V  |  GND → GND
 *
 *  LIBRARY: MFRC522 by GithubCommunity (Arduino Library Manager)
 * ═══════════════════════════════════════════════════════════════
 */

#include <SPI.h>
#include <MFRC522.h>

#define SS_PIN     10
#define RST_PIN     9
#define IR_PIN      2   // active-low: LOW = obstacle detected
#define BUZZER_PIN  3   // passive buzzer signal pin

// ── Buzzer configuration ────────────────────────────────────────
#define BUZZ_FREQ_HI   1500   // Hz — high tone (for MOTION)
#define BUZZ_FREQ_LO   1000   // Hz — low tone (for CLEAR)
#define BUZZ_DURATION   200   // ms — duration of each tone burst

MFRC522 rfid(SS_PIN, RST_PIN);
MFRC522::MIFARE_Key key;

// ── Storage locations ────────────────────────────────────────────
// MIFARE Classic  : block 1 (sector 0, safe writable block)
// NTAG / Ultralight: starting at user page 4 (first user-data page)
//   Each page = 4 bytes, item code up to 16 chars spans pages 4-7
const byte CLASSIC_BLOCK   = 1;
const byte NTAG_START_PAGE = 4;
const byte NTAG_PAGES      = 4;   // 4 pages × 4 bytes = 16 bytes max

const unsigned long CARD_TIMEOUT_MS = 7000;
const byte          MAX_AUTH_TRIES  = 3;

String inputBuffer  = "";
bool   lastIrPinState = false;   // tracks previous IR pin state for edge detection
bool   systemTriggered = false;  // tracks the toggle state (true = MOTION, false = CLEAR)

// ════════════════════════════════════════════════════════════════
void setup() {
  Serial.begin(9600);
  SPI.begin();
  reInitReader();
  delay(100);
  pinMode(IR_PIN, INPUT);      // IR obstacle sensor
  pinMode(BUZZER_PIN, OUTPUT); // passive buzzer
  digitalWrite(BUZZER_PIN, LOW);
  Serial.println("READY");
}

void loop() {
  // ── Serial command reader ────────────────────────────────────
  while (Serial.available()) {
    char c = (char)Serial.read();
    if (c == '\n') {
      inputBuffer.trim();
      if (inputBuffer.length() > 0) handleCommand(inputBuffer);
      inputBuffer = "";
    } else if (c != '\r') {
      inputBuffer += c;
      // Safety: discard buffer if it grows beyond any valid command length.
      // This prevents partial/garbage fragments from contaminating the next cmd.
      if (inputBuffer.length() > 24) inputBuffer = "";
    }
  }

  // ── IR Sensor (Toggle Logic) ───────────────
  // A single pass of the hand (or button press) counts as one trigger.
  // First trigger sends MOTION, second trigger sends CLEAR, and so on.
  bool currentIrState = (digitalRead(IR_PIN) == LOW);
  
  // Detect leading edge (object just entered the sensor path)
  if (currentIrState && !lastIrPinState) {
    systemTriggered = !systemTriggered; // Toggle state
    
    if (systemTriggered) {
      Serial.println("MOTION");
      soundBuzzer(BUZZ_FREQ_HI);
    } else {
      Serial.println("CLEAR");
      soundBuzzer(BUZZ_FREQ_LO);
    }
  }
  lastIrPinState = currentIrState;
}

// ════════════════════════════════════════════════════════════════
//  Re-init reader  — call before EVERY scan to wipe stale state
// ════════════════════════════════════════════════════════════════
void reInitReader() {
  rfid.PCD_StopCrypto1();
  rfid.PCD_Init();
  rfid.PCD_SetAntennaGain(rfid.RxGain_max);
  // 100 ms gives the MFRC522 RF field time to fully stabilise.
  // 50 ms was occasionally too short when cards were re-presented quickly.
  delay(100);
}

// ════════════════════════════════════════════════════════════════
//  Command dispatcher
// ════════════════════════════════════════════════════════════════
void handleCommand(const String& cmd) {
  if (cmd == "PING") {
    Serial.println("PONG");

  } else if (cmd == "READ") {
    doRead();

  } else if (cmd.startsWith("WRITE:")) {
    String payload = cmd.substring(6);
    payload.trim();
    if (payload.length() == 0)   { Serial.println("ERROR:Empty item code"); return; }
    if (payload.length() > 16)   { Serial.println("ERROR:Max 16 characters"); return; }
    doWrite(payload);

  } else if (cmd == "BUZZ_TEST") {
    soundBuzzer(BUZZ_FREQ_HI);
    Serial.println("BUZZ_OK");

  } else {
    Serial.println("ERROR:Unknown command");
  }
}

// ════════════════════════════════════════════════════════════════
//  Wait for card, return tag kind: 'C'=Classic, 'U'=Ultralight/NTAG, 0=timeout
// ════════════════════════════════════════════════════════════════
char waitForCard(unsigned long timeoutMs) {
  reInitReader();
  unsigned long start = millis();

  while (millis() - start < timeoutMs) {
    if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {
      delay(50);  // settle after anti-collision

      MFRC522::PICC_Type piccType = rfid.PICC_GetType(rfid.uid.sak);

      // Print tag type to serial for user awareness
      Serial.print("TAG_TYPE:");
      Serial.println(rfid.PICC_GetTypeName(piccType));

      switch (piccType) {
        case MFRC522::PICC_TYPE_MIFARE_MINI:
        case MFRC522::PICC_TYPE_MIFARE_1K:
        case MFRC522::PICC_TYPE_MIFARE_4K:
          return 'C';   // Classic — needs key auth

        case MFRC522::PICC_TYPE_MIFARE_UL:   // Ultralight / NTAG
          return 'U';

        default:
          // Unknown / unsupported (e.g. ISO 14443-4, FeliCa)
          Serial.print("ERROR:Unsupported tag type: ");
          Serial.println(rfid.PICC_GetTypeName(piccType));
          haltCard();
          return 0;
      }
    }
    delay(100);
  }
  return 0;   // timeout
}

// ════════════════════════════════════════════════════════════════
//  READ  (dispatcher)
// ════════════════════════════════════════════════════════════════
void doRead() {
  char kind = waitForCard(CARD_TIMEOUT_MS);
  if      (kind == 'C') readClassic();
  else if (kind == 'U') readNTAG();
  else                  Serial.println("NOTAG");
}

// ════════════════════════════════════════════════════════════════
//  WRITE (dispatcher)
// ════════════════════════════════════════════════════════════════
void doWrite(const String& code) {
  char kind = waitForCard(CARD_TIMEOUT_MS);
  if      (kind == 'C') writeClassic(code);
  else if (kind == 'U') writeNTAG(code);
  else                  Serial.println("NOTAG");
}

// ════════════════════════════════════════════════════════════════
//  MIFARE CLASSIC  helpers
// ════════════════════════════════════════════════════════════════

MFRC522::StatusCode classicAuth(byte block) {
  // Prepare default key every call (safe to redo)
  for (byte i = 0; i < 6; i++) key.keyByte[i] = 0xFF;

  MFRC522::StatusCode status = MFRC522::STATUS_ERROR;
  for (byte t = 0; t < MAX_AUTH_TRIES; t++) {
    status = rfid.PCD_Authenticate(
      MFRC522::PICC_CMD_MF_AUTH_KEY_A, block, &key, &rfid.uid);
    if (status == MFRC522::STATUS_OK) return status;
    rfid.PCD_StopCrypto1();
    delay(100);
  }
  return status;
}

void readClassic() {
  MFRC522::StatusCode status = classicAuth(CLASSIC_BLOCK);
  if (status != MFRC522::STATUS_OK) {
    Serial.print("ERROR:Auth failed - ");
    Serial.println(rfid.GetStatusCodeName(status));
    haltCard(); return;
  }

  byte buf[18]; byte sz = sizeof(buf);
  status = rfid.MIFARE_Read(CLASSIC_BLOCK, buf, &sz);
  if (status != MFRC522::STATUS_OK) {
    Serial.print("ERROR:Read failed - ");
    Serial.println(rfid.GetStatusCodeName(status));
    haltCard(); return;
  }

  emitCode(buf, 16);
  haltCard();
}

void writeClassic(const String& code) {
  MFRC522::StatusCode status = classicAuth(CLASSIC_BLOCK);
  if (status != MFRC522::STATUS_OK) {
    Serial.print("ERROR:Auth failed - ");
    Serial.println(rfid.GetStatusCodeName(status));
    haltCard(); return;
  }

  byte buf[16]; memset(buf, 0, 16);
  for (byte i = 0; i < code.length() && i < 16; i++) buf[i] = code[i];

  status = rfid.MIFARE_Write(CLASSIC_BLOCK, buf, 16);
  if (status != MFRC522::STATUS_OK) {
    Serial.print("WRITE_FAIL:");
    Serial.println(rfid.GetStatusCodeName(status));
  } else {
    Serial.println("WRITE_OK");
  }
  haltCard();
}

// ════════════════════════════════════════════════════════════════
//  NTAG213 / NTAG215 / NTAG216 / MIFARE Ultralight  helpers
//  (NO authentication — plain page read/write)
//  Page = 4 bytes.  Pages 0-3 are reserved on NTAG / Ultralight.
//  We use pages 4-7  → 16 bytes total for the item code.
// ════════════════════════════════════════════════════════════════

void readNTAG() {
  // Collect NTAG_PAGES × 4 bytes
  byte allBytes[16]; memset(allBytes, 0, 16);

  for (byte p = 0; p < NTAG_PAGES; p++) {
    byte buf[18]; byte sz = sizeof(buf);
    // MIFARE_Read on UL/NTAG reads 4 pages at once starting from the given page,
    // but we only use the first 4 bytes of each call.
    MFRC522::StatusCode status =
      rfid.MIFARE_Read(NTAG_START_PAGE + p, buf, &sz);

    if (status != MFRC522::STATUS_OK) {
      Serial.print("ERROR:Read page ");
      Serial.print(NTAG_START_PAGE + p);
      Serial.print(" failed - ");
      Serial.println(rfid.GetStatusCodeName(status));
      haltCard(); return;
    }
    // buf[0..3] = requested page data
    for (byte b = 0; b < 4; b++) allBytes[p * 4 + b] = buf[b];
  }

  emitCode(allBytes, 16);
  haltCard();
}

void writeNTAG(const String& code) {
  // Pad to 16 bytes
  byte allBytes[16]; memset(allBytes, 0, 16);
  for (byte i = 0; i < code.length() && i < 16; i++) allBytes[i] = code[i];

  // Write 4 bytes per page (NTAG page size)
  for (byte p = 0; p < NTAG_PAGES; p++) {
    byte pageBuf[4];
    for (byte b = 0; b < 4; b++) pageBuf[b] = allBytes[p * 4 + b];

    MFRC522::StatusCode status =
      rfid.MIFARE_Ultralight_Write(NTAG_START_PAGE + p, pageBuf, 4);

    if (status != MFRC522::STATUS_OK) {
      Serial.print("WRITE_FAIL:Page ");
      Serial.print(NTAG_START_PAGE + p);
      Serial.print(" - ");
      Serial.println(rfid.GetStatusCodeName(status));
      haltCard(); return;
    }
  }

  Serial.println("WRITE_OK");
  haltCard();
}

// ════════════════════════════════════════════════════════════════
//  Extract printable ASCII string from raw bytes and send to PC
// ════════════════════════════════════════════════════════════════
void emitCode(byte* buf, byte len) {
  char itemCode[17]; memset(itemCode, 0, 17);
  for (byte i = 0; i < len && i < 16; i++) {
    if (buf[i] == 0x00 || buf[i] == 0xFF) break;   // end of data / unprogrammed
    if (buf[i] >= 0x20 && buf[i] <= 0x7E) itemCode[i] = (char)buf[i];
  }
  String code = String(itemCode);
  code.trim();
  if (code.length() == 0) {
    Serial.println("ERROR:Tag empty - write an item code first");
  } else {
    Serial.print("CARD:");
    Serial.println(code);
  }
}

// ════════════════════════════════════════════════════════════════
//  Halt card + reset reader to clean state
// ════════════════════════════════════════════════════════════════
void haltCard() {
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
  rfid.PCD_Init();
  rfid.PCD_SetAntennaGain(rfid.RxGain_max);
}

// ════════════════════════════════════════════════════════════════
//  Buzzer alert — single tone short beep
// ════════════════════════════════════════════════════════════════
void soundBuzzer(int frequency) {
  tone(BUZZER_PIN, frequency, BUZZ_DURATION);
  delay(BUZZ_DURATION + 50); // Small blocking delay to ensure beep clarity
  noTone(BUZZER_PIN);
  digitalWrite(BUZZER_PIN, LOW);   // ensure pin is low when idle
}

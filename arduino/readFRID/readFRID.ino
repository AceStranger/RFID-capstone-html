#include <SPI.h>
#include <MFRC522.h>
#include <Wire.h>

#define RST_PIN         9           // Configurable, see typical pin layout above
#define SS_PIN          10          // Configurable, see typical pin layout above
#define LED_PIN         2           // Pin for LED
#define BUZZER_PIN      3           // Pin for piezo buzzer

MFRC522 mfrc522(SS_PIN, RST_PIN);   // Create MFRC522 instance

void setup() {
  Serial.begin(9600);                // Initialize serial communications with the PC
  SPI.begin();                       // Init SPI bus
  mfrc522.PCD_Init();                // Init MFRC522 card
  // Serial.println(F("Ready to read cards..."));
  pinMode(LED_PIN, OUTPUT);          // Set LED pin as output
  pinMode(BUZZER_PIN, OUTPUT);       // Set buzzer pin as output
}

void loop() {
  // Check if a new card is present
  if (!mfrc522.PICC_IsNewCardPresent() || !mfrc522.PICC_ReadCardSerial()) {
    return;
  }

  // Indicate card detection with LED and buzzer
  digitalWrite(LED_PIN, HIGH);
  tone(BUZZER_PIN, 1000); // Play a tone for 100 ms
  delay(100);
  digitalWrite(LED_PIN, LOW);
  noTone(BUZZER_PIN);

  // Read and display the UID of the card
  String content = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    content.concat(String(mfrc522.uid.uidByte[i] < 0x10 ? " 0" : " "));
    content.concat(String(mfrc522.uid.uidByte[i], HEX));
  }
  content.toUpperCase();
  Serial.println("Card UID: " + content.substring(1));

  // Halt the card and stop encryption
  mfrc522.PICC_HaltA();
  mfrc522.PCD_StopCrypto1();

  // Ensure the reader resets for the next scan
  mfrc522.PCD_Init(); // Reinitialize the reader

  // Small delay to avoid immediate retrigger
  delay(200);
}

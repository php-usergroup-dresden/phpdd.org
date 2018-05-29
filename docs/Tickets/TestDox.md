💚 Passed | 💔 Error | 💔 Failure | 🧡 Warning | 💛 Risky | 💙 Incomplete | 💜 Skipped

# Test suite: /Users/hollodotme/Sites/php-usergroup-dresden/phpdd.org/tests

* Environment: `Development`  
* Base namespace: `PHPUGDD\PHPDD\Website\Tests\Tickets`  

## Unit\Application\Bridges\UserInputTest

- [x] Can Get Values Trimmed (💚 1)

---

## Unit\Application\Tickets\DiscountItemCollectionTest

- [x] Can Rewind Collection (💚 1)
- [x] Current (💚 1)
- [x] Add (💚 1)
- [x] Key (💚 1)
- [x] Valid (💚 1)
- [x] Count (💚 1)
- [x] Next (💚 1)

---

## Unit\Application\Tickets\DiscountItemTest

- [x] Can Construct From Values (💚 1)
- [x] Can Check If Discount Is Allowed For ACertain Ticket Name (💚 2)

---

## Unit\Application\Tickets\TicketAvailabilityValidatorTest

- [x] Returns Fale If Ticket Config Was Not Found (💚 1)
- [x] Can Check If Tickets Are Available (💚 1)

---

## Unit\Application\Tickets\TicketItemCollectionTest

- [x] Can Get Count For Ticket (💚 1)
- [x] Can Get Count For Type And Attendee Name (💚 1)
- [x] Can Get Count For Type (💚 1)
- [x] Can Iterate Over Collection (💚 1)

---

## Unit\Application\Tickets\TicketItemTest

- [x] Can Create Instance (💚 1)
- [x] Can Grant Discounts (💚 3)
- [x] Throws Exception If Discount Exceeds Ticket Price (💚 1)
- [x] Grant Discount Throws Exception If Ticket Is Not Allowed For Discount (💚 1)

---

## Unit\Application\Tickets\TicketOrderBillingAddressTest

- [x] Can Get Address As String (💚 1)
- [x] Can Get Address Values (💚 1)

---

## Unit\Application\Tickets\TicketOrderTest

- [x] Can Create Instance From Order Id And Date (💚 1)
- [x] Ticket Order Gets Placeable If Email Billing Address And Tickets Were Set (💚 1)
- [x] Throws Exception For Exceeding Max Conference Ticket Count (💚 1)
- [x] Throws Exception For Exceeding Max Workshop Ticket Count (💚 1)
- [x] Throws Exception For Exceeding Max Workshop Ticket Count Per Attendee (💚 1)
- [x] Throws Exception For Exceeding Max Conference Ticket Count Per Attendee (💚 1)
- [x] Same Attendee Can Order AWorkshop Ticket For Each Slot (💚 1)
- [x] Can Get Totals (💚 1)
- [x] Ticket Items Without Discount Item Do Not Add Discounts (💚 1)

---

## Unit\Application\Tickets\TicketTest

- [x] Can Get Values (💚 1)
- [x] Can Check If Tickets Are Equal (💚 1)
- [x] Tickets Are Not Equal If Ticket Id Is Different (💚 1)

---

## Unit\Application\Types\AddressAddonTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\AttendeeNameTest

- [x] Empty Attendee Name Throw Exception (💚 6)

---

## Unit\Application\Types\CityTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\CompanyNameTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\CountryCodeTest

- [x] Can Create Instance For Valid Country Codes (💚 202)
- [x] Throws Exception For Invalid Country Code (💚 1)

---

## Unit\Application\Types\DiscountCodeTest

- [x] Can Create Instance From Valid Codes (💚 3)
- [x] Can Generate ADiscount Code (💚 1)
- [x] Throws Exception For Invalid Codes (💚 3)

---

## Unit\Application\Types\DiscountDescriptionTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\DiscountNameTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\DiscountPriceTest

- [x] Can Create Instance From Valid Money (💚 3)
- [x] Throws Exception For Positive Money Amount (💚 1)

---

## Unit\Application\Types\DiversityDonationTest

- [x] Can Create Instance From Valid Money (💚 3)
- [x] Throws Exception For Negative Money (💚 1)

---

## Unit\Application\Types\FirstnameTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\LastnameTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\StreetWithNumberTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\TicketDescriptionTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\TicketIdTest

- [x] Throws Exception When Constructed With Invalid Ticket Id (💚 9)

---

## Unit\Application\Types\TicketNameTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\TicketOrderDiscountTotalTest

- [x] Can Create Instance From Valid Money (💚 3)
- [x] Throws Exception For Negative Money (💚 1)

---

## Unit\Application\Types\TicketOrderEmailAddressTest

- [x] Can Create Instance For Valid Email Addresses (💚 1)
- [x] Throws Exception For Invalid Email Address (💚 1)

---

## Unit\Application\Types\TicketOrderPaymentTotalTest

- [x] Can Create Instance From Valid Money (💚 3)
- [x] Throws Exception For Negative Money (💚 1)

---

## Unit\Application\Types\TicketOrderTotalTest

- [x] Can Create Instance From Valid Money (💚 3)
- [x] Throws Exception For Negative Money (💚 1)

---

## Unit\Application\Types\TicketPriceTest

- [x] Can Create Instance From Valid Money (💚 2)
- [x] Throws Exception For Zero Money (💚 1)
- [x] Throws Exception For Negative Money (💚 1)

---

## Unit\Application\Types\TicketTypeTest

- [x] Can Create Instance For Valid Ticket Types (💚 5)
- [x] Throws Exception For Invalid Ticket Type (💚 1)

---

## Unit\Application\Types\VatNumberTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Types\ZipCodeTest

- [x] Throws Exception When Constructed With Empty String (💚 6)

---

## Unit\Application\Web\Tickets\Write\Validators\AttendeeValidatorTest

- [x] Validation Fails For Empty Attendee Name (💚 1)
- [x] Validation Passes (💚 1)

---

## Unit\Application\Web\Tickets\Write\Validators\BillingInformationValidatorTest

- [x] Validation Fails For Empty Input (💚 1)
- [x] Validation Fails If ACompany Outside Germany Does Not Provide AVat Number (💚 1)
- [x] Validation Fails If The Repeated Email Is Not The Same (💚 1)
- [x] Validation Passes (💚 1)

---

## Unit\Application\Web\Tickets\Write\Validators\DiscountCodeValidatorTest

- [x] Validation Fails For Invalid Discount Code (💚 1)
- [x] Validation Passes (💚 1)

---

## Unit\Application\Web\Tickets\Write\Validators\SelectTicketsValidatorTest

- [x] Validation Of Ticket Selection Fails (💚 5)
- [x] Validation Ticket Selection Passes (💚 1)

---

Report created at 2018-05-29 23:34:41 (UTC)
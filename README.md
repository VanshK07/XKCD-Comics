XKCD
This project is a PHP-based email verification system where users register using their email, receive a verification code, and subscribe to get a random XKCD comic every day. A CRON job fetches a random XKCD comic and sends it to all registered users every 24 hours.

🚀 Your Task
Your objective is to implement the functionality in the src/ directory while following these rules:

✅ DO NOT change function names or modify the file structure.

✅ DO NOT modify anything outside the src/ folder. You can add additional files if required inside src folder.

✅ DO NOT hardcode emails; use registered_emails.txt as the database.

✅ Implement all required functions in functions.php.

✅ Implement a form in index.php to take email input and verify via code.

✅ Implement a CRON job to send XKCD comics to registered users every 24 hours.

✅ Implement an unsubscribe feature where users can opt out via email verification.

✅ Implement unsubscribe.php to handle email unsubscription.

📝 Submission Steps [ Non adherence to this will cause disqualification ]
Clone the repository to your local machine.
Create a new branch from the main branch. Do not push code directly to main.
Implement the required features inside the src/ directory.
Push your code to your branch (not main).
Raise a Pull Request (PR) only once against the main branch when all your code is finalized.
Do not raise multiple PRs.
Do not add multiple commits to a PR after submission.
Failure to follow these instructions will result in disqualification.
Wait for your submission to be reviewed. Do not merge the PR.
⚠ Important Notes
All form elements should always be visible on the page and should not be conditionally rendered. This ensures the assignment can be tested properly at the appropriate steps.

Please ensure that if the base repository shows the original template repo, update it so that your repo's main branch is set as the base branch.

Recommended PHP version: 8.3

📌 Features to Implement
⿡ Email Verification
Users enter their email in a form.
A 6-digit numeric code is generated and emailed to them.
Users enter the code in the form to verify and register.
Store the verified email in registered_emails.txt.
⿢ Unsubscribe Mechanism
Emails should include an unsubscribe link.
Clicking it will take user to the unsubscribe page.
Users enter their email in a form.
A 6-digit numeric code is generated and emailed to them.
Users enter the code to confirm unsubscription.
⿣ XKCD Comic Subscription
Every 24 hours, cron job should:
Fetch data from https://xkcd.com/[randomComicID]/info.0.json.
Format it as HTML (not JSON).
Send it via email to all registered users.
📜 File Details & Function Stubs
You must implement the following functions inside functions.php:

function generateVerificationCode() {
    // Generate and return a 6-digit numeric code
}

function registerEmail($email) {
    $file = _DIR_ . '/registered_emails.txt';
    // Save verified email to registered_emails.txt
}

function unsubscribeEmail($email) {
    $file = _DIR_ . '/registered_emails.txt';
    // Remove email from registered_emails.txt
}

function sendVerificationEmail($email, $code) {
    // Send an email containing the verification code
}

function verifyCode($email, $code) {
    // Check if the provided code matches the sent one
}

function fetchAndFormatXKCDData() {
    // Fetch latest data from XKCD API and format as HTML
}

function sendXKCDUpdatesToSubscribers() {
    $file = _DIR_ . '/registered_emails.txt';
    // Send formatted XKCD data to all registered emails
}
🔄 CRON Job Implementation
📌 You must implement a CRON job that runs cron.php every 24 hours.

📌 Do not just write instructions—provide an actual setup_cron.sh script inside src/.

📌 Your script should automatically configure the CRON job on execution.

🛠 Required Files
setup_cron.sh (Must configure the CRON job)
cron.php (Must handle sending XKCD comics)
🚀 How It Should Work
The setup_cron.sh script should register a CRON job that executes cron.php every 24 hours.
The CRON job must be automatically added when the script runs.
The cron.php file should actually fetch random XKCD comic and send emails to registered users.
📩 Email Handling
✅ The email content must be in HTML format (not JSON).

✅ Use PHP's mail() function for sending emails.

✅ Each email should include an unsubscribe link.

✅ Unsubscribing should trigger a confirmation code before removal.

✅ Store emails in registered_emails.txt (Do not use a database).

❌ Disqualification Criteria
🚫 Hardcoding verification codes.

🚫 Using a database (use registered_emails.txt).

🚫 Modifying anything outside the src/ directory.

🚫 Changing function names.

🚫 Not implementing a working CRON job.

🚫 Not formatting emails as HTML.

📌 Input & Button Formatting Guidelines
📧 Email Input & Submission Button:
The email input field must have name="email".
The submit button must have id="submit-email".
✅ Example:
<input type="email" name="email" required>
<button id="submit-email">Submit</button>
🔢 Verification Code Input & Submission Button:
The verification input field must have name="verification_code".
The submit button must have id="submit-verification".
✅ Example:
<input type="text" name="verification_code" maxlength="6" required>
<button id="submit-verification">Verify</button>
🚫 Unsubscribe Email & Submission Button
The unsubscribe input field must have name="unsubscribe_email".
The submit button must have id="submit-unsubscribe".
✅ Example:
<input type="email" name="unsubscribe_email" required>
<button id="submit-unsubscribe">Unsubscribe</button>
🚫 Unsubscribe Code Input & Submission Button
The unsubscribe code input field must have name="verification_code".
The submit button must have id="submit-verification".
✅ Example:
<input type="text" name="verification_code" maxlength="6" required>
<button id="submit-verification">Verify</button>
📩 Email Content Guidelines
✅ Verification Email:
Subject: Your Verification Code
Body Format:
<p>Your verification code is: <strong>123456</strong></p>
Sender: no-reply@example.com
📩 Email Content Guidelines
⚠ Note: The Subject and Body of the email must strictly follow the formats below, including the exact HTML structure.

✅ XKCD Comic Email:
Subject: Your XKCD Comic
Body Format:
<h2>XKCD Comic</h2>
<img src="image_url_here" alt="XKCD Comic">
<p><a href="#" id="unsubscribe-button">Unsubscribe</a></p>
✅ Unsubscribe Confirmation Email:
Subject: Confirm Un-subscription
Body Format:
<p>To confirm un-subscription, use this code: <strong>654321</strong></p>

<?php if($_settings->chk_flashdata('success')): ?>
<script>
    alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif; ?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f0f4f8;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 40px;
        width: 500px;
    }

    h1 {
        text-align: center;
        margin-bottom: 30px;
        font-size: 24px;
        color: #333;
    }

    .form-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-section {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-section h2 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    label {
        font-size: 14px;
        margin-bottom: 6px;
        display: block;
    }

    input, textarea {
        width: 100%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 12px;
    }

    button {
        padding: 12px 20px;
        font-size: 16px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
    }

    button:hover {
        background-color: #218838;
    }

    button:disabled {
        background-color: #ccc;
    }

    .feedback {
        text-align: center;
        margin-top: 20px;
        font-size: 16px;
    }

    .feedback.success {
        color: green;
    }

    .feedback.error {
        color: red;
    }
</style>

<div class="container">
    <h1>Send SMS Messages</h1>

    <div class="form-container">
        <!-- Single message form -->
        <div class="form-section">
            <h2>Send to Single Contact</h2>
            <form id="singleMessageForm">
                <label for="singleContact">Recipient's Contact</label>
                <input type="text" id="singleContact" name="contact" placeholder="Enter recipient's phone number" required>
                
                <label for="singleMessage">Message</label>
                <textarea id="singleMessage" name="message" placeholder="Enter your message" required></textarea>
                
                <button type="submit">Send SMS</button>
            </form>
        </div>

        <!-- Send to all contacts -->
        <div class="form-section">
            <h2>Send to All Contacts</h2>
            <form id="sendAllForm">
                <label for="allMessage">Message</label>
                <textarea id="allMessage" name="message" placeholder="Enter your message for all contacts" required></textarea>
                
                <button type="submit">Send to All</button>
            </form>
        </div>

        <!-- Feedback message -->
        <div class="feedback" id="feedbackMessage"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Single message form submission
        $('#singleMessageForm').submit(function(e) {
            e.preventDefault();

            const contact = $('#singleContact').val();
            const message = $('#singleMessage').val();

            $.ajax({
                url: 'Clients.php?f=send_single_sms',  // Backend PHP file handling SMS sending
                method: 'POST',
                data: {
                    contact: contact,
                    message: message
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert_toast("SMS sent successfully.", 'success');
                        $('#singleContact').val('');
                        $('#singleMessage').val('');
                    } else if (result.status === 'error') {
                        alert_toast("An error occurred while sending SMS.", 'error');
                    } else {
                        alert_toast("Unexpected response from server.", 'error');
                    }
                },
                error: function() {
                    alert_toast("An error occurred while processing the request.", 'error');
                }
            });
        });

        // Bulk message form submission
        $('#sendAllForm').submit(function(e) {
            e.preventDefault();

            const message = $('#allMessage').val();

            $.ajax({
                url: 'Clients.php?f=send_bulk_sms',
                method: 'POST',
                data: {
                    message: message
                },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        alert_toast("Bulk SMS sent successfully.", 'success');
                        $('#allMessage').val('');
                    } else if (result.status === 'partial') {
                        alert_toast("Partial success: Some messages failed.", 'error');
                    } else {
                        alert_toast("An error occurred while sending bulk SMS.", 'error');
                    }
                },
                error: function() {
                    alert_toast("An error occurred while processing the request.", 'error');
                }
            });
        });
    });
</script>

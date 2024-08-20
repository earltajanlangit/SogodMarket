<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Login</title>
    <style>
       #uni_modal .modal-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
}

#uni_modal .modal-content>.modal-footer, 
#uni_modal .modal-content>.modal-header {
    display: none;

}
        
        
        .login-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .viewport {
            border: 1px solid #ced4da;
            border-radius: 8px;
            width: 100%;
            height: 300px;
            margin: 20px 0;
            background-color: #000;
        }
        .qr-detected-container {
            display: none;
            margin-top: 20px;
        }
        .btn-primary, .btn-dark {
            border-radius: 5px;
            margin: 10px;
            padding: 10px 20px;
        }
        h2 {
            margin-bottom: 15px;
            color: #343a40;
        }
        p {
            margin-bottom: 20px;
            color: #6c757d;
        }
        h4 {
            margin-bottom: 15px;
            color: #28a745;
        }
    </style>
</head>

    <div class="main">
        <!-- Login Area -->
        <div class="login-container">
            <h2>Welcome Back!</h2>
            <p>Login through QR code scanner.</p>
            <video id="interactive" class="viewport" width="100%" style="display: none;"></video>
            <button id="back-btn" class="btn btn-primary "> Back </button>
            <div class="qr-detected-container">
                <h4>QR Code Detected!</h4>
                <form action="./endpoint/login.php" method="POST">
                    <input type="hidden" id="detected-qr-code" name="qr-code">
                    <button type="submit" class="btn btn-dark">Login</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <!-- Instascan JS -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <script>
        let scanner;

        function startScanner() {
            console.log("Starting the scanner...");

            const video = document.getElementById('interactive');
            video.style.display = 'block';

            if (!video) {
                console.error('Video element not found.');
                return;
            }

            scanner = new Instascan.Scanner({ video: video });

            scanner.addListener('scan', function (content) {
                console.log('QR Code scanned:', content);
                $("#detected-qr-code").val(content);
                scanner.stop();
                
                const detectedContainer = document.querySelector(".qr-detected-container");
                if (detectedContainer) {
                    console.log('Showing QR Detected Container');
                    detectedContainer.style.display = 'block'; 
                } else {
                    console.error('QR Detected Container not found.');
                }
                
                video.style.display = 'none'; 
            });

            Instascan.Camera.getCameras()
                .then(function (cameras) {
                    console.log('Cameras found:', cameras);
                    if (cameras.length > 0) {
                        scanner.start(cameras[0])
                            .then(() => {
                                console.log('Scanner started successfully.');
                            })
                            .catch(function (err) {
                                console.error('Error starting scanner:', err);
                            });
                    } else {
                        console.error('No cameras found.');
                        alert('No cameras found.');
                    }
                })
                .catch(function (err) {
                    console.error('Camera access error:', err);
                    alert('Camera access error: ' + err);
                });
        }

        $(document).ready(function() {
            startScanner(); // Automatically start the scanner when the page loads

            $('#back-btn').click(function() {
                console.log('Back button clicked.');
                window.location.href = "index.php";
                // uni_modal("", "login.php", "mid-large");
            });
        });
    </script>
</body>
</html>
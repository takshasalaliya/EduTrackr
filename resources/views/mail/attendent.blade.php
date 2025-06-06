<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Attendance Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            padding: 20px;
            color: #333;
        }
        .container {
            background: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        h2 {
            color: #007bff;
        }
        a.button {
            display: inline-block;
            margin: 15px 0;
            padding: 10px 18px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🌟 Dear Parents 🌟</h2>

        <p>👆 Kindly refer to the attendance file link below 📋</p>

        <a href="{{ $link }}" class="button" target="_blank">📄 Click Here to View Attendance</a>

        <hr>

        <h4>🔴 Important Reminder:</h4>
        <ul>
            <li>You are required to maintain <strong>75% attendance</strong>.</li>
            <li>You will be responsible for any action taken by the college/university in case of low attendance.</li>
        </ul>

        <h4>💡 A Thought to Reflect On:</h4>
        <p>
            Attendance is not just a number; it reflects commitment, responsibility, and readiness for future challenges. 📚✨
        </p>
        <p>
            We kindly request you to discuss the importance of regular attendance with your ward and encourage them to stay consistent in attending all sessions. Together, we can ensure their success and growth!
        </p>

        <p class="footer">
            For any queries, feel free to contact:<br>
            📞 Class Counselor
        </p>

        <p><strong>Let’s aim for excellence together! 💪</strong></p>
    </div>
</body>
</html>

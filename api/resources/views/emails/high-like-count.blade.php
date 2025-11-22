<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>High Like Count Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #FF6B6B;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 20px;
        }
        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .stats {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .stat-row:last-child {
            border-bottom: none;
        }
        .stat-label {
            font-weight: bold;
            color: #666;
        }
        .stat-value {
            color: #333;
        }
        .milestone {
            color: #FF6B6B;
            font-weight: bold;
            font-size: 18px;
        }
        .footer {
            background-color: #f5f5f5;
            color: #666;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            border-radius: 0 0 8px 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 High Like Count Alert</h1>
        </div>

        <div class="content">
            <p>Hello Admin,</p>

            <div class="alert-box">
                <strong>⚠️ Milestone Reached:</strong> A user has surpassed the like threshold of {{ $threshold }} likes!
            </div>

            <div class="stats">
                <div class="stat-row">
                    <span class="stat-label">User Name:</span>
                    <span class="stat-value">{{ $user->name }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">User Email:</span>
                    <span class="stat-value">{{ $user->email }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Location:</span>
                    <span class="stat-value">{{ $user->location ?? 'N/A' }}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Age:</span>
                    <span class="stat-value">{{ $user->age ?? 'N/A' }} years</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Current Like Count:</span>
                    <span class="milestone">❤️ {{ $likeCount }} likes</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Member Since:</span>
                    <span class="stat-value">{{ $user->created_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>

            <p>
                This is an automated notification. The user <strong>{{ $user->name }}</strong> has received 
                <span class="milestone">{{ $likeCount }} likes</span>, exceeding the threshold of {{ $threshold }} likes.
            </p>

            <p>
                You can manage user accounts and monitor their activity from your admin dashboard.
            </p>

            <p>
                Best regards,<br>
                <strong>Tinder Clone System</strong>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated email notification. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Tinder Clone. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

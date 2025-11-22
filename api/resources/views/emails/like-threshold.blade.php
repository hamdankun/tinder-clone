<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f5f5f5;">
    <div style="background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="color: #333; margin-bottom: 20px;">🎉 User Like Threshold Alert</h2>
        
        <p style="color: #666; line-height: 1.6;">
            Hello Admin,
        </p>

        <p style="color: #666; line-height: 1.6;">
            The user <strong>{{ $user->name }}</strong> (ID: {{ $user->id }}) has reached <strong style="color: #e91e63;">{{ $likeCount }} likes</strong>!
        </p>

        <div style="background-color: #f9f9f9; padding: 20px; border-left: 4px solid #e91e63; margin: 20px 0; border-radius: 4px;">
            <h3 style="color: #333; margin-top: 0;">User Details:</h3>
            <ul style="color: #666; line-height: 2;">
                <li><strong>Name:</strong> {{ $user->name }}</li>
                <li><strong>Email:</strong> {{ $user->email }}</li>
                <li><strong>Age:</strong> {{ $user->age }}</li>
                <li><strong>Location:</strong> {{ $user->location }}</li>
                <li><strong>Bio:</strong> {{ $user->bio ?? 'N/A' }}</li>
                <li><strong>Total Likes Received:</strong> <strong style="color: #e91e63;">{{ $likeCount }}</strong></li>
                <li><strong>Member Since:</strong> {{ $user->created_at->format('M d, Y') }}</li>
            </ul>
        </div>

        <p style="color: #666; line-height: 1.6;">
            This user has become a popular member of the Tinder Clone community! You may want to consider featuring their profile or reaching out with special offers.
        </p>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <p style="color: #999; font-size: 12px;">
                This is an automated notification from Tinder Clone. Please do not reply to this email.
            </p>
        </div>
    </div>
</div>

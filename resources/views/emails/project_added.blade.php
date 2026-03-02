<!DOCTYPE html>
<html>
<head>
    <title>New Project Assignment</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333;">
    <h2>Hello!</h2>
    <p>This is an automated notification to inform you that you have been added to a new project.</p>
    
    <div style="background: #f4f7f6; padding: 20px; border-radius: 8px; border-left: 4px solid #4a90e2;">
        <p><strong>Project Name:</strong> {{ $project->name }}</p>
        <p><strong>Project Owner:</strong> {{ $ownerName }}</p>
    </div>

    <p>You can now view this project and start claiming tasks from your Collaboration Dashboard.</p>
    
    <p><em>No action is required from your side to accept this.</em></p>
    
    <hr style="border: none; border-top: 1px solid #eee; margin: 20px 0;">
    <p style="font-size: 0.8em; color: #888;">This is a system-generated notification from your Collaboration App.</p>
</body>
</html>
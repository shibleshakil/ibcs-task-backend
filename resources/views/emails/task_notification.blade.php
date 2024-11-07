<!DOCTYPE html>
<html>
<head>
    <title>Task Notification</title>
</head>
<body>
    <h1>{{ $task->title }}</h1>
    <p>{{ $task->description }}</p>
    <p>Priority: {{ ucfirst($task->priority) }}</p>
    <p>Deadline: {{ $task->deadline }}</p>
    <p>Status: {{ ucfirst($task->status ?? 'pending') }}</p>
</body>
</html>

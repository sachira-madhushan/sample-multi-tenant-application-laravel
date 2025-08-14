<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Welcome, {{ Auth::user()->name }}</h1>
            <a href="{{ route('logout') }}" class="bg-red-500 text-white p-2 rounded-md hover:bg-red-600">Logout</a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-semibold mb-4">Create a New Post</h2>
            <form action="{{ route('posts.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-bold mb-2">Post Title</label>
                    <input type="text" name="title" id="title" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" required>
                </div>
                <div class="mb-4">
                    <label for="content" class="block text-gray-700 font-bold mb-2">Post Content</label>
                    <textarea name="content" id="content" rows="5" class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300" required></textarea>
                </div>
                <button type="submit" class="bg-green-500 text-white p-2 rounded-md hover:bg-green-600">Create Post</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Your Posts</h2>
            @forelse ($posts as $post)
                <div class="border-b pb-4 mb-4">
                    <h3 class="text-xl font-bold">{{ $post->title }}</h3>
                    <p class="text-gray-700">{{ $post->content }}</p>
                    <small class="text-gray-500">Created at: {{ $post->created_at->format('M d, Y') }}</small>
                </div>
            @empty
                <p>You haven't created any posts yet.</p>
            @endforelse
        </div>
    </div>
</body>
</html>

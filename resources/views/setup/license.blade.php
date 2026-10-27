<!DOCTYPE html>
<html>
<head>
    <title>Software Activation</title>
    <style>
        body { font-family: Arial; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
        input, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
        button { background: #007BFF; color: white; border: none; cursor: pointer; border-radius: 4px; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Activate Your Software</h2>
        <p>Please enter your purchase details to unlock the project.</p>
        
        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('license.verify') }}" method="POST">
            @csrf
            <label>Envato Username</label>
            <input type="text" name="username" required placeholder="e.g. fablead_admin">
            
            <label>Purchase Code</label>
            <input type="text" name="purchase_code" required placeholder="e.g. 59182473-LQXM-...">
            
            <button type="submit">Verify & Activate</button>
        </form>
    </div>
</body>
</html>

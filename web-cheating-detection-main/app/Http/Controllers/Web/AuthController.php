<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    private string $apiBase = 'https://bgnuf22eight.com/Exam-app/exam-evaluation-app/public/api';

    public function showLogin()
    {
        if (session('user')) {
            return $this->redirectByRole(session('user')['role']);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);

        $input = $request->email;
        $body = ['password' => $request->password];
        if (str_contains($input, '@')) {
            $body['email'] = $input;
        } else {
            $body['rollno'] = $input;
        }

        try {
            $curlOpts = [
                'force_ip_resolve' => 'v4',
                'verify'           => false,
                'curl'             => [CURLOPT_RESOLVE => ['bgnuf22eight.com:443:159.198.67.59']],
            ];
            $response = Http::withOptions($curlOpts)->timeout(30)->post("{$this->apiBase}/login", $body);

            $data = $response->json();

            if ($response->successful() && (isset($data['user']) || isset($data['role']))) {
                $user = $data['user'] ?? null;
                if (!$user) {
                    return back()->withErrors(['email' => 'Invalid response from server'])->withInput();
                }
                session([
                    'user' => [
                        'id'     => $user['id'],
                        'name'   => $user['name'],
                        'email'  => $user['email'] ?? '',
                        'role'   => $user['role'] ?? $data['role'],
                        'rollno' => $user['rollno'] ?? null,
                    ]
                ]);
                return $this->redirectByRole($user['role'] ?? $data['role']);
            }

            $message = $data['message'] ?? 'Invalid credentials';
            
            // Check if account email is not verified (requires OTP popup)
            $msgLower = strtolower($message);
            if (str_contains($msgLower, 'verify') || str_contains($msgLower, 'otp') || str_contains($msgLower, 'unverified') || str_contains($msgLower, 'not verified')) {
                return back()
                    ->with('show_otp_modal', true)
                    ->with('unverified_email', $input)
                    ->withErrors(['email' => $message])
                    ->withInput();
            }

            return back()->withErrors(['email' => $message])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Connection error: ' . $e->getMessage()])->withInput();
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'otp'   => 'required',
        ]);

        try {
            $curlOpts = [
                'force_ip_resolve' => 'v4',
                'verify'           => false,
                'curl'             => [CURLOPT_RESOLVE => ['bgnuf22eight.com:443:159.198.67.59']],
            ];
            $response = Http::withOptions($curlOpts)->timeout(30)->post("{$this->apiBase}/verify-otp", [
                'email' => strtolower(trim($request->email)),
                'otp'   => trim($request->otp),
            ]);

            $data = $response->json();

            if ($data && (isset($data['status']) && $data['status'] == true)) {
                return response()->json([
                    'status'  => true,
                    'message' => $data['message'] ?? 'Email verified successfully! You can now log in.',
                    'user'    => $data['user'] ?? null,
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => $data['message'] ?? 'Invalid OTP Code. Please check your Gmail inbox.',
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Verification error: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function redirectByRole(string $role)
    {
        return match($role) {
            'admin'   => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            default   => redirect('/'),
        };
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Validação básica sem verificação de email único (que usa PDO)
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'terms' => ['required', 'accepted'],
            ]);
            
            // Verificar manualmente se o e-mail já existe usando mysqli
            $this->checkEmailExists($request->email);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            event(new Registered($user));
    
            Auth::login($user);
    
            return redirect(RouteServiceProvider::HOME);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // Trate outros erros
            // Salvar o erro em um arquivo de log personalizado
            $logPath = storage_path('logs/register_errors.log');
            file_put_contents(
                $logPath, 
                date('Y-m-d H:i:s') . ' - Erro: ' . $e->getMessage() . ' - ' . $e->getTraceAsString() . "\n", 
                FILE_APPEND
            );
            
            // Retornar com erro para facilitar a depuração
            return back()->withInput($request->except('password'))
                ->withErrors(['email' => 'Ocorreu um erro ao processar seu registro. Por favor, tente novamente mais tarde.']);
        }
    }
    
    /**
     * Verifica se o e-mail já existe usando mysqli
     * 
     * @param string $email
     * @throws ValidationException
     */
    private function checkEmailExists($email): void
    {
        // Use mysqli para verificar diretamente no banco de dados
        $host = env('DB_HOST', 'localhost');
        $database = env('DB_DATABASE', 'allsegte_recursos');
        $username = env('DB_USERNAME', 'allsegte_recurso_root');
        $password = env('DB_PASSWORD', '@Pedro9cce22f2');
        
        // Criar conexão
        try {
            $mysqli = mysqli_connect($host, $username, $password, $database);
            
            if (!$mysqli) {
                // Log do erro de conexão
                $logPath = storage_path('logs/mysqli_errors.log');
                file_put_contents(
                    $logPath, 
                    date('Y-m-d H:i:s') . ' - Erro de conexão MySQL: ' . mysqli_connect_error() . "\n", 
                    FILE_APPEND
                );
                
                throw ValidationException::withMessages([
                    'email' => 'Não foi possível verificar o e-mail. Por favor, tente novamente mais tarde.'
                ]);
            }
            
            // Consulta segura usando prepared statement
            $stmt = mysqli_prepare($mysqli, "SELECT COUNT(*) as count FROM users WHERE email = ?");
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($mysqli);
            
            if ($row['count'] > 0) {
                throw ValidationException::withMessages([
                    'email' => 'Este endereço de e-mail já está sendo utilizado.'
                ]);
            }
        } catch (\Exception $e) {
            // Se não conseguir verificar com mysqli, deixa passar
            // e confiar na integridade do banco de dados
            $logPath = storage_path('logs/mysqli_errors.log');
            file_put_contents(
                $logPath, 
                date('Y-m-d H:i:s') . ' - Erro ao verificar e-mail: ' . $e->getMessage() . "\n", 
                FILE_APPEND
            );
        }
    }
}

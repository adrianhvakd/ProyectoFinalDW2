<?php

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    public $type;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $mostrarConfirmarCodigo = false;
    public $code;
    private $generatedCode;

    public function mount($type)
    {
        $this->type = $type;
    }

    public function login()
    {
        $data = $this->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($data)) {
            if (Auth::user()->active == false) {
                Auth::logout();
                flash()->use('theme.aurora')->option('timeout', 3000)->error('Su cuenta ha sido dada de baja. Por favor, contacte al administrador.');
                return redirect()->route('login');
            }
            session()->regenerate();
            flash()->use('theme.aurora')->option('timeout', 3000)->success('Inicio de sesión exitoso');
            return redirect()->route('dashboard');
        }

        $this->addError('email', 'Credenciales inválidas.');
    }

    public function register()
    {
        $data = $this->validate([
            'name' => 'required|string|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $this->generatedCode = random_int(10000, 99999);

        $this->mostrarConfirmarCodigo = true;

        flash()
            ->use('theme.aurora')
            ->option('timeout', 10000)
            ->info("Código de confirmación: {$this->generatedCode}");

        session([
            'register_data' => $data,
            'register_code' => $this->generatedCode,
        ]);
    }

    public function confirmarCodigo()
    {
        $this->validate([
            'code' => 'required|digits:5',
        ]);

        $data = session('register_data');
        $storedCode = session('register_code');

        if ($this->code != $storedCode) {
            $this->addError('code', 'El código ingresado es incorrecto.');
            return;
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        session()->forget(['register_data', 'register_code']);

        flash()->use('theme.aurora')->option('timeout', 3000)->success('Registro exitoso');

        return redirect()->route('home');
    }
};

?>

<div class="flex-1 flex items-center justify-center bg-base-200">
    <div class="card w-full max-w-xs shadow-2xl bg-base-100">
        <div class="card-body">

            <h2 class="text-2xl font-bold text-center">
                {{ $type == 'login' ? 'Iniciar Sesión' : 'Registrarse' }}
            </h2>

            @if ($type == 'login')
                <form wire:submit.prevent="login" class="space-y-4">
                    <div class="form-control">
                        <label class="label">Email</label>
                        <input type="email" wire:model.defer="email" class="input input-bordered w-full">
                        @error('email')
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label">Contraseña</label>
                        <input type="password" wire:model.defer="password" class="input input-bordered w-full">
                        @error('password')
                            <span class="text-error text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mt-4">
                        <button type="submit" class="btn btn-primary w-full">Iniciar Sesión</button>
                    </div>
                </form>
            @else
                @if (!$mostrarConfirmarCodigo)
                    <form wire:submit.prevent="register" class="space-y-4">
                        <div class="form-control">
                            <label class="label">Nombre</label>
                            <input type="text" wire:model.defer="name" class="input input-bordered w-full">
                            @error('name')
                                <span class="text-error text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">Email</label>
                            <input type="email" wire:model.defer="email" class="input input-bordered w-full">
                            @error('email')
                                <span class="text-error text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">Contraseña</label>
                            <input type="password" wire:model.defer="password" class="input input-bordered w-full">
                            @error('password')
                                <span class="text-error text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">Confirmar Contraseña</label>
                            <input type="password" wire:model.defer="password_confirmation"
                                class="input input-bordered w-full">
                        </div>

                        <div class="form-control mt-4">
                            <button type="submit" class="btn btn-primary w-full">Registrarse</button>
                        </div>
                    </form>
                @else
                    <form wire:submit.prevent="confirmarCodigo" class="space-y-4">
                        <div class="form-control">
                            <label class="label">Ingresa el código de confirmación</label>
                            <input type="text" wire:model.defer="code" class="input input-bordered w-full"
                                maxlength="5">
                            @error('code')
                                <span class="text-error text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control mt-4">
                            <button type="submit" class="btn btn-primary w-full">Confirmar Código</button>
                        </div>
                    </form>
                @endif
            @endif

            <div class="mt-3 text-center text-sm">
                @if ($type == 'login')
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}" class="link link-primary">Regístrate</a>
                @else
                    ¿Ya tienes cuenta?
                    <a href="{{ route('login') }}" class="link link-primary">Iniciar Sesión</a>
                @endif
            </div>

        </div>
    </div>
</div>

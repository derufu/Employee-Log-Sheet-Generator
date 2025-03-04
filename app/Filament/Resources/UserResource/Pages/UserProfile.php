<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.resources.user-resource.pages.user-profile';

    public $name;
    public $email;
    public $image;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $user = Auth::user();

        // Populate form with current user data
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'image' => $user->image, // Filament automatically binds the stored path
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            FileUpload::make('image')
                ->label('Profile Image')
                ->image()
                ->disk('public')
                ->directory('profile-images')
                ->maxSize(1024)
                ->preserveFilenames()
                ->rules(['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'])
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                ->imagePreviewHeight('250')
                ->imageCropAspectRatio('1:1')
                ->imageResizeTargetWidth('500')
                ->imageResizeTargetHeight('500'),

            TextInput::make('name')
                ->label('Full Name')
                ->required(),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required(),

            TextInput::make('password')
                ->label('New Password')
                ->password()
                ->nullable(),

            TextInput::make('password_confirmation')
                ->label('Confirm Password')
                ->password()
                ->same('password')
                ->nullable(),
        ]);
    }

    public function save()
    {
        $user = Auth::user();
        $validatedData = $this->form->getState(); // Retrieve form data

        // Handle password update
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Handle image upload
        // if (!empty($validatedData['image']) && $validatedData['image'] instanceof \Illuminate\Http\UploadedFile) {
        //     $validatedData['image'] = $validatedData['image']->store('profile-images', 'public');
        // }

        $user->update($validatedData);

        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();
    }

    protected function getFormModel(): Model|string|null
    {
        return Auth::user();
    }
}

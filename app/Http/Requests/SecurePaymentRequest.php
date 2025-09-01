<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class SecurePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Vérifier le rate limiting pour les paiements
        return !$this->hasExceededPaymentRateLimit();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'payment_method' => [
                'required',
                'string',
                'in:card',
            ],
            'card_number' => [
                'required',
                'string',
                'regex:/^[0-9\s]{13,19}$/',
                function ($attribute, $value, $fail) {
                    if (!$this->isValidCardNumber($value)) {
                        $fail('Le numéro de carte est invalide.');
                    }
                },
            ],
            'card_expiry' => [
                'required',
                'string',
                'regex:/^(0[1-9]|1[0-2])\/([0-9]{2})$/',
                function ($attribute, $value, $fail) {
                    if (!$this->isValidExpiryDate($value)) {
                        $fail('La date d\'expiration est invalide.');
                    }
                },
            ],
            'card_cvv' => [
                'required',
                'string',
                'regex:/^[0-9]{3,4}$/',
            ],
            'card_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s\-\'\.]+$/',
            ],
        ];
    }

    /**
     * Préparer les données pour la validation
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le numéro de carte
        if ($this->card_number) {
            $this->merge([
                'card_number' => preg_replace('/\s+/', '', $this->card_number),
            ]);
        }

        // Nettoyer le nom sur la carte
        if ($this->card_name) {
            $this->merge([
                'card_name' => trim(preg_replace('/\s+/', ' ', $this->card_name)),
            ]);
        }
    }

    /**
     * Messages d'erreur personnalisés
     */
    public function messages(): array
    {
        return [
            'payment_method.required' => 'Le mode de paiement est requis.',
            'payment_method.in' => 'Le mode de paiement sélectionné est invalide.',
            'card_number.required' => 'Le numéro de carte est requis.',
            'card_number.regex' => 'Le format du numéro de carte est invalide.',
            'card_expiry.required' => 'La date d\'expiration est requise.',
            'card_expiry.regex' => 'Le format de la date d\'expiration doit être MM/AA.',
            'card_cvv.required' => 'Le code CVV est requis.',
            'card_cvv.regex' => 'Le code CVV doit contenir 3 ou 4 chiffres.',
            'card_name.required' => 'Le nom sur la carte est requis.',
            'card_name.regex' => 'Le nom sur la carte contient des caractères invalides.',
        ];
    }

    /**
     * Gérer une tentative de validation échouée
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Logger les tentatives de validation échouées
        Log::channel('security')->warning('Validation de paiement échouée', [
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'errors' => $validator->errors()->toArray(),
            'data' => $this->except(['card_number', 'card_cvv']), // Exclure les données sensibles
            'timestamp' => now(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Vérifier si le rate limit pour les paiements est dépassé
     */
    private function hasExceededPaymentRateLimit(): bool
    {
        $key = 'payment_attempts:' . $this->ip();
        $attempts = cache()->get($key, 0);
        
        if ($attempts >= 3) {
            Log::channel('security')->alert('Tentatives de paiement excessives', [
                'ip' => $this->ip(),
                'attempts' => $attempts,
                'timestamp' => now(),
            ]);
            return true;
        }

        // Incrémenter le compteur
        cache()->put($key, $attempts + 1, now()->addMinutes(15));
        
        return false;
    }

    /**
     * Valider le numéro de carte avec l'algorithme de Luhn
     */
    private function isValidCardNumber(string $cardNumber): bool
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        
        if (!ctype_digit($cardNumber) || strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
            return false;
        }

        // Algorithme de Luhn
        $sum = 0;
        $length = strlen($cardNumber);
        
        for ($i = $length - 1; $i >= 0; $i--) {
            $digit = (int) $cardNumber[$i];
            
            if (($length - $i) % 2 === 0) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            
            $sum += $digit;
        }
        
        return $sum % 10 === 0;
    }

    /**
     * Valider la date d'expiration
     */
    private function isValidExpiryDate(string $expiry): bool
    {
        if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry, $matches)) {
            return false;
        }

        $month = (int) $matches[1];
        $year = (int) $matches[2] + 2000;
        
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');
        
        // Vérifier que la date n'est pas dans le passé
        if ($year < $currentYear || ($year === $currentYear && $month < $currentMonth)) {
            return false;
        }
        
        // Vérifier que la date n'est pas trop loin dans le futur (max 20 ans)
        if ($year > $currentYear + 20) {
            return false;
        }
        
        return true;
    }
}

@extends('layouts.guest')

@section('title', 'Verifikasi Email')

@section('content')
<div class="min-h-[calc(100vh-91px)] flex items-center justify-center bg-gradient-to-b from-[#D8E7FF] via-[#DDEAFF] to-white py-10 px-4">
    <div class="w-full max-w-[560px]">
        <div class="bg-white rounded-[28px] shadow-lg px-5 py-8 sm:px-10 sm:py-10">

            <div class="flex justify-center mb-5">
                <img src="{{ asset('images/logo-tegal.png') }}" alt="Logo" class="h-[64px] w-[52px] object-contain">
            </div>

            <div class="text-center mb-7">
                <h2 class="text-[28px] sm:text-[32px] font-extrabold text-black leading-tight">Verifikasi Email</h2>
                <p class="text-base sm:text-lg font-normal text-gray mt-3 leading-7">
                    Masukkan kode OTP 6 digit yang dikirim ke<br class="hidden sm:block">
                    <span class="font-bold text-black break-all">{{ $maskedEmail }}</span>
                </p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 text-green-700 border border-green-200 px-4 py-3 rounded-2xl mb-5 text-sm text-center leading-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-2xl mb-5 text-sm text-center leading-6">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->has('resend'))
                <div class="bg-amber-50 text-amber-700 border border-amber-200 px-4 py-3 rounded-2xl mb-5 text-sm text-center leading-6">
                    {{ $errors->first('resend') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.otp.verify') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-base font-bold text-black mb-4 leading-5">Kode OTP</label>
                    @php
                        $oldOtp = preg_replace('/\D/', '', old('otp', ''));
                    @endphp
                    <input type="hidden" name="otp" id="otp-value" value="{{ $oldOtp }}">
                    <div class="grid grid-cols-6 gap-2 sm:gap-4" id="otp-boxes">
                        @for($i = 0; $i < 6; $i++)
                            <input type="text"
                                   inputmode="numeric"
                                   pattern="[0-9]*"
                                   maxlength="1"
                                   value="{{ substr($oldOtp, $i, 1) }}"
                                   aria-label="Digit OTP {{ $i + 1 }}"
                                   class="otp-digit h-[56px] w-full min-w-0 rounded-[14px] border border-[#D7E4EA] bg-[#DDF3F8] text-center text-xl font-extrabold text-[#0F172A] shadow-sm outline-none transition focus:border-[#24C6BE] focus:ring-2 focus:ring-[#24C6BE]/30 sm:h-[68px] sm:text-2xl">
                        @endfor
                    </div>
                    @error('otp')
                        <p class="text-red-500 text-xs mt-3 text-center">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-primary text-white font-semibold text-sm h-[54px] rounded-[18px] hover:bg-primary-hover transition">
                    Verifikasi Email
                </button>
            </form>

            <form method="POST" action="{{ route('register.otp.resend') }}" class="mt-4">
                @csrf
                <button type="submit" id="resend-otp-button"
                        data-seconds="{{ $resendSecondsRemaining ?? 0 }}"
                        class="w-full bg-[#EFF6FF] text-primary font-semibold text-sm h-[50px] rounded-[18px] hover:bg-[#DBEAFE] transition">
                    Kirim Ulang OTP
                </button>
            </form>

            <p class="text-center text-sm text-gray leading-7 mt-5">
                Sudah terverifikasi?
                <a href="{{ route('login') }}" class="text-primary font-bold hover:underline">Masuk</a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const boxes = Array.from(document.querySelectorAll('.otp-digit'));
    const hiddenInput = document.getElementById('otp-value');
    const resendButton = document.getElementById('resend-otp-button');

    function onlyDigits(value) {
        return value.replace(/\D/g, '');
    }

    function syncOtpValue() {
        hiddenInput.value = boxes.map((box) => box.value).join('');
    }

    boxes.forEach((box, index) => {
        box.addEventListener('input', (event) => {
            const digits = onlyDigits(event.target.value);

            if (digits.length > 1) {
                digits.slice(0, boxes.length).split('').forEach((digit, digitIndex) => {
                    boxes[digitIndex].value = digit;
                });
                boxes[Math.min(digits.length, boxes.length) - 1].focus();
                syncOtpValue();
                return;
            }

            box.value = digits;
            syncOtpValue();

            if (digits && index < boxes.length - 1) {
                boxes[index + 1].focus();
            }
        });

        box.addEventListener('keydown', (event) => {
            if (event.key === 'Backspace' && !box.value && index > 0) {
                boxes[index - 1].focus();
            }
        });

        box.addEventListener('paste', (event) => {
            event.preventDefault();
            const digits = onlyDigits(event.clipboardData.getData('text')).slice(0, boxes.length);

            digits.split('').forEach((digit, digitIndex) => {
                boxes[digitIndex].value = digit;
            });

            boxes[Math.min(digits.length, boxes.length) - 1]?.focus();
            syncOtpValue();
        });
    });

    syncOtpValue();

    let seconds = Number(resendButton.dataset.seconds || 0);
    const defaultText = 'Kirim Ulang OTP';

    function renderCountdown() {
        if (seconds > 0) {
            resendButton.disabled = true;
            resendButton.classList.add('cursor-not-allowed', 'opacity-70');
            resendButton.textContent = `Kirim ulang OTP dalam ${seconds} detik`;
            seconds -= 1;
            window.setTimeout(renderCountdown, 1000);
            return;
        }

        resendButton.disabled = false;
        resendButton.classList.remove('cursor-not-allowed', 'opacity-70');
        resendButton.textContent = defaultText;
    }

    renderCountdown();
});
</script>
@endsection

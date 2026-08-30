@php($isDanger = $notification->getStatus() === 'danger')

<div
    x-data="notificationComponent({ notification: @js($notification->toArray()) })"
    x-on:keydown.escape.window="close()"
    x-transition:enter-start="fi-transition-enter-start"
    x-transition:enter-end="fi-transition-enter-end"
    x-transition:leave-start="fi-transition-leave-start"
    x-transition:leave-end="fi-transition-leave-end"
    @class([
        'pib-creation-success',
        'pib-creation-success--danger' => $isDanger,
    ])
    role="dialog"
    aria-modal="true"
    aria-labelledby="pib-creation-success-title-{{ $notification->getId() }}"
>
    <div class="pib-creation-success__backdrop" aria-hidden="true"></div>

    <section class="pib-creation-success__panel">
        <div class="pib-creation-success__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25">
                @if ($isDanger)
                    <path stroke-linecap="round" d="M12 7v6" />
                    <path stroke-linecap="round" d="M12 17h.01" />
                    <circle cx="12" cy="12" r="9" />
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                @endif
            </svg>
        </div>

        <h2
            id="pib-creation-success-title-{{ $notification->getId() }}"
            class="pib-creation-success__title"
        >
            {{ $notification->getTitle() }}
        </h2>

        @if (filled($notification->getBody()))
            <p class="pib-creation-success__body">{{ $notification->getBody() }}</p>
        @endif

        <button
            x-ref="confirmationButton"
            x-init="$nextTick(() => $refs.confirmationButton.focus())"
            x-on:click="close()"
            type="button"
            class="pib-creation-success__button"
        >
            {{ $isDanger ? 'Revisar formulário' : 'Continuar' }}
        </button>
    </section>

    <style>
        .pib-creation-success {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: grid;
            place-items: center;
            padding: 1.25rem;
            pointer-events: auto;
            transition: opacity 180ms ease, transform 180ms ease;
        }

        .pib-creation-success__backdrop {
            position: absolute;
            inset: 0;
            background: rgb(17 24 39 / 58%);
            backdrop-filter: blur(3px);
        }

        .pib-creation-success__panel {
            position: relative;
            width: min(100%, 28rem);
            padding: 2rem;
            border: 1px solid rgb(217 119 6 / 22%);
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 24px 70px rgb(17 24 39 / 28%);
            text-align: center;
        }

        .pib-creation-success__icon {
            display: grid;
            width: 3.5rem;
            height: 3.5rem;
            margin: 0 auto 1rem;
            place-items: center;
            border-radius: 999px;
            background: #fef3c7;
            color: #b45309;
        }

        .pib-creation-success__icon svg {
            width: 1.75rem;
            height: 1.75rem;
        }

        .pib-creation-success--danger .pib-creation-success__panel {
            border-color: rgb(220 38 38 / 30%);
        }

        .pib-creation-success--danger .pib-creation-success__icon {
            background: #fee2e2;
            color: #b91c1c;
        }

        .pib-creation-success--danger .pib-creation-success__button {
            background: #b91c1c;
            color: #fff;
        }

        .pib-creation-success--danger .pib-creation-success__button:hover {
            background: #991b1b;
        }

        .pib-creation-success--danger .pib-creation-success__button:focus-visible {
            outline-color: rgb(220 38 38 / 35%);
        }

        .pib-creation-success__title {
            color: #111827;
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.4;
        }

        .pib-creation-success__body {
            margin-top: .5rem;
            color: #4b5563;
            font-size: .925rem;
            line-height: 1.55;
        }

        .pib-creation-success__button {
            width: 100%;
            margin-top: 1.5rem;
            padding: .7rem 1rem;
            border-radius: .625rem;
            background: #d59d16;
            color: #111827;
            font-weight: 700;
            transition: background 150ms ease, box-shadow 150ms ease;
        }

        .pib-creation-success__button:hover {
            background: #e2ae2b;
        }

        .pib-creation-success__button:focus-visible {
            outline: 3px solid rgb(213 157 22 / 35%);
            outline-offset: 3px;
        }

        .dark .pib-creation-success__panel {
            border-color: rgb(245 158 11 / 28%);
            background: #18181b;
        }

        .dark .pib-creation-success__title {
            color: #fafafa;
        }

        .dark .pib-creation-success__body {
            color: #d4d4d8;
        }
    </style>
</div>

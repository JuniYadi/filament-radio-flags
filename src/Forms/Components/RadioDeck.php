<?php

namespace JuniYadi\RadioFlags\Forms\Components;

use Closure;
use Filament\Support\Concerns\HasAlignment;
use Filament\Support\Concerns\HasColor;
use Filament\Support\Concerns\HasIcon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use JuniYadi\RadioFlags\Contracts\HasDescriptions;
use JuniYadi\RadioFlags\Contracts\HasIcons;
use JuniYadi\RadioFlags\Intermediary\IntermediaryRadio;
use JuniYadi\RadioFlags\Traits\HasDirection;
use JuniYadi\RadioFlags\Traits\HasExtraCardsAttributes;
use JuniYadi\RadioFlags\Traits\HasExtraDescriptionsAttributes;
use JuniYadi\RadioFlags\Traits\HasExtraOptionsAttributes;
use JuniYadi\RadioFlags\Traits\HasGap;
use JuniYadi\RadioFlags\Traits\HasIconSizes;
use JuniYadi\RadioFlags\Traits\HasPadding;

class RadioDeck extends IntermediaryRadio
{
    use HasAlignment;
    use HasColor;
    use HasDirection;
    use HasExtraCardsAttributes;
    use HasExtraDescriptionsAttributes;
    use HasExtraOptionsAttributes;
    use HasGap;
    use HasIcon;
    use HasIconSizes;
    use HasPadding;

    protected array|Arrayable|Closure|string|null $icons = null;

    protected array|Arrayable|Closure|string $descriptions = [];

    protected bool|Closure $isMultiple = false;

    protected string $view = 'radio-deck::forms.components.radio-deck';

    protected function setUp(): void
    {
        parent::setUp();

        $this->default(fn (RadioDeck $component): mixed => $component->isMultiple() ? [] : null);

        $this->afterStateHydrated(static function (RadioDeck $component, $state): void {
            if (! $component->isMultiple()) {
                return;
            }

            if (is_array($state)) {
                return;
            }

            $component->state([]);
        });
    }

    public function icons(array|Arrayable|string|Closure|null $icons): static
    {
        $this->icons = $icons;

        return $this;
    }

    public function descriptions(array|Arrayable|string|Closure $descriptions): static
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    public function multiple(bool|Closure $condition = true): static
    {
        $this->isMultiple = $condition;

        return $this;
    }

    /**
     * @param  array-key  $value
     */
    public function hasIcons($value): bool
    {
        if ($value !== null && ! empty($this->getIcons())) {
            return array_key_exists($value, $this->getIcons());
        }

        return false;
    }

    /**
     * @return array | Closure | null
     */
    public function getIcons(): mixed
    {
        $icons = $this->evaluate($this->icons);

        $enum = $icons;

        if (is_string($enum) && enum_exists($enum)) {
            if (is_a($enum, HasIcons::class, allow_string: true)) {
                return collect($enum::cases())
                    ->mapWithKeys(fn ($case) => [
                        ($case?->value ?? $case->name) => $case->getIcons() ?? $case->name,
                    ])
                    ->all();
            }

            return collect($enum::cases())
                ->mapWithKeys(fn ($case) => [
                    ($case?->value ?? $case->name) => $case->name,
                ])
                ->all();
        }

        if ($icons instanceof Arrayable) {
            $icons = $icons->toArray();
        }

        return $icons;
    }

    public function getIcon($value): ?string
    {
        return $this->getIcons()[$value] ?? null;
    }

    /**
     * @return array<string | Htmlable>
     */
    public function getDescriptions(): array
    {
        $descriptions = $this->evaluate($this->descriptions);

        $enum = $descriptions;

        if (is_string($enum) && enum_exists($enum)) {
            if (is_a($enum, HasDescriptions::class, allow_string: true)) {
                return collect($enum::cases())
                    ->mapWithKeys(fn ($case) => [
                        ($case?->value ?? $case->name) => $case->getDescriptions() ?? $case->name,
                    ])
                    ->all();
            }

            return collect($enum::cases())
                ->mapWithKeys(fn ($case) => [
                    ($case?->value ?? $case->name) => $case->name,
                ])
                ->all();
        }

        if ($descriptions instanceof Arrayable) {
            $descriptions = $descriptions->toArray();
        }

        return $descriptions;
    }

    public function isMultiple(): bool
    {
        return (bool) $this->evaluate($this->isMultiple);
    }
}

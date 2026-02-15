<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Services\SettingsService;

class ManageInvoiceSettings extends Component
{
    public $prefix;
    public $reset;  
    public $next_number;
    public $format;
    public $date_format;
    public $due_days_default;

    public $footer_note;
    public $terms;

    public $show_logo;
    public $show_tax_number;

    public $paper;    
    public $template; 

    public function mount(SettingsService $settings): void
    {
        $this->prefix          = $settings->get('invoice.prefix', 'INV');
        $this->reset           = $settings->get('invoice.numbering.reset', 'yearly');
        $this->next_number     = (int) $settings->get('invoice.next_number', 1);
        $this->format          = $settings->get('invoice.format', 'INV-{YYYY}-{000000}');
        $this->date_format     = $settings->get('invoice.date_format', 'd/m/Y');
        $this->due_days_default= (int) $settings->get('invoice.due_days_default', 0);

        $this->footer_note     = $settings->get('invoice.footer_note', '');
        $this->terms           = $settings->get('invoice.terms', '');

        $this->show_logo       = (bool) $settings->get('invoice.show_logo', '1');
        $this->show_tax_number = (bool) $settings->get('invoice.show_tax_number', '1');

        $this->paper           = $settings->get('invoice.print.paper', 'A4');
        $this->template        = $settings->get('invoice.print.template', 'default');
    }

    protected function rules(): array
    {
        return [
            'prefix' => ['required','string','max:10'],
            'reset' => ['required','in:never,yearly'],
            'next_number' => ['required','integer','min:1','max:999999999'],
            'format' => ['required','string','max:60'],
            'date_format' => ['required','string','max:20'],
            'due_days_default' => ['required','integer','min:0','max:365'],

            'footer_note' => ['nullable','string','max:1000'],
            'terms' => ['nullable','string','max:2000'],

            'show_logo' => ['boolean'],
            'show_tax_number' => ['boolean'],

            'paper' => ['required','in:A4,thermal'],
            'template' => ['required','string','max:30'],
        ];
    }

    public function save(SettingsService $settings): void
    {
        $this->validate();

        if (!str_contains($this->format, '{000000}') && !preg_match('/\{0+\}/', $this->format)) {
            $this->addError('format', 'Format must include a zero-padding placeholder like {000000}.');
            return;
        }

        $settings->setMany([
            'invoice.prefix' => $this->prefix,
            'invoice.numbering.reset' => $this->reset,
            'invoice.next_number' => (string) $this->next_number,
            'invoice.format' => $this->format,
            'invoice.date_format' => $this->date_format,
            'invoice.due_days_default' => (string) $this->due_days_default,

            'invoice.footer_note' => $this->footer_note,
            'invoice.terms' => $this->terms,

            'invoice.show_logo' => $this->show_logo ? '1' : '0',
            'invoice.show_tax_number' => $this->show_tax_number ? '1' : '0',

            'invoice.print.paper' => $this->paper,
            'invoice.print.template' => $this->template,
        ]);

        session()->flash('message', 'Invoice settings saved.');
        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.settings.manage-invoice-settings');
    }
}

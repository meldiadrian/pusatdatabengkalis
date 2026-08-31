<?php

namespace App\Filament\Resources\PemohonResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
////
//use Filament\Forms\Components\Wizard\Step;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PemohonResource;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use App\Models\Konfirmasi;
use App\Jobs\SendWhatsAppMessage;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;



class CreatePemohon extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = PemohonResource::class;

    protected function wizard(): Wizard
    {
        return Wizard::make($this->getSteps())
            ->backButtonLabel('Kembali')
            ->nextButtonLabel('Selanjutnya');
    }

    // protected function afterCreate(): void
    // {
    //     // set status pending di pemohon
    //     $this->record->update([
    //         'status' => 'pending',
    //     ]);

    //     // insert ke tabel konfirmasi
    //     Konfirmasi::create([
    //         'pemohon_id' => $this->record->id,
    //         'status' => 'pending',
    //     ]);

    //     //-------------------------------------------------
    //     SendWhatsAppMessage::dispatch(
    //         $this->record->phone,
    //         'Data berhasil dibuat'
    //     );


    //     //     //whatssapp notifikasi
    //     $token = config('services.whatsapp.token');
    //     $url   = config('services.whatsapp.url');
    //     $record = $this->record;

    //     $nosekre = User::whereRole('sekre')->first()?->no_hp;
    //     $message =
    //         "🔔 *Informasi Permohonan Data Baru!*\n\n" .
    //         "Yth. Bapak/Ibu,\n\n" .
    //         "Terdapat *permohonan data baru* yang telah diajukan dan membutuhkan tindak lanjut.\n" .
    //         "Mohon untuk segera diproses ke tahap selanjutnya.\n\n"

    //         // .  "📌 *Tujuan Permohonan Data:* " . (
    //         //     $record->unitKerja
    //         //     ?? optional($record->opd)->nama_opd
    //         //     ?? '-'
    //         // ) . "\n"
    //         // . "👤 *Instansi Yang Dituju:* " . (
    //         //     $record->opd_tujuan
    //         //     ?? optional($record->unitKerja)->nama_opd
    //         //     ?? '-'
    //         // ) . "\n"
    //         // . "📂 *Data Yang Dibutuhkan:* {$record->data_diminta}\n"
    //         // . "📊 *Tujuan Penggunaan Data:* {$record->tujuan_penggunaan}\n"
    //         . "🕐 Waktu: " . Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s');
    //     Http::post($url . '/message/send-text', [
    //         "session"  => $token,
    //         $nosekre = User::whereRole('sekre')->first()?->no_hp,
    //         app(WhatsAppService::class)->send($nosekre, $message),
    //         "text" => $message,
    //     ]);
    // }

    protected function afterCreate(): void
    {
        // Update status
        $this->record->update([
            'status' => 'pending',
        ]);

        // Simpan konfirmasi
        Konfirmasi::create([
            'pemohon_id' => $this->record->id,
            'status'     => 'pending',
        ]);

        // ===========================
        // WA ke Pemohon
        // ===========================
        if (!blank($this->record->phone)) {
            SendWhatsAppMessage::dispatch(
                $this->record->phone,
                "✅ Permohonan data berhasil dibuat dan sedang menunggu proses verifikasi."
            );
        } else {
            Log::warning('WA Pemohon tidak terkirim: phone kosong', [
                'pemohon_id' => $this->record->id,
            ]);
        }

        // ===========================
        // WA ke Sekretariat
        // ===========================
        $sekre = User::query()
            ->whereRole('sekre')
            ->first();

        if (!blank($sekre?->no_hp)) {

            $message =
              "🔔 *INFORMASI PERMOHONAN DATA BARU*\n\n"
            . "Yth. Bapak/Ibu,\n\n"
            . "Terdapat permohonan data baru yang telah diajukan dan saat ini membutuhkan tindak lanjut.\n"
            . "Mohon untuk segera melakukan pengecekan dan proses ke tahap selanjutnya.\n\n"
            . "Terima kasih.\n\n"
           
            . "🕐 Waktu: " . Carbon::now('Asia/Jakarta')->format('d-m-Y H:i:s');

            SendWhatsAppMessage::dispatch(
                $sekre->no_hp,
                $message
            );
        } else {
            Log::warning('WA Sekretariat tidak terkirim: no_hp kosong', [
                'user_id' => $sekre?->id,
            ]);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'pending';
        return $data;
    }

    public function getHeading(): string
    {
        return '';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function getCreatedNotificationTitle(): ?string
    // {
    //     return 'Data Bersahil Ditambah';
    // }


    protected function getSavedNotification(): ?Notification
    {
        return null; // matikan notif bawaan
    }

    protected function afterSave(): void
    {
        Notification::make()
            ->title('Data berhasil disimpan!')
            ->body('Data telah diperbarui dan ditampilkan.')
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Set instansi_pemohon dari unit_kerja_id user yang login
        $data['instansi_pemohon'] = auth()->user()->unit_kerja_id;
        return $data;
    }

    protected function handleRecordCreation(array $data): \App\Models\Pemohon
    {
        // Memastikan instansi_pemohon diset sebelum create
        $data['instansi_pemohon'] = auth()->user()->unit_kerja_id;
        return parent::handleRecordCreation($data);
    }

    protected function getSteps(): array
    {
        return [

            Step::make('Instansi Pemohon')
                ->schema([
                    Select::make('instansi_pemohon')
                        ->label('Pilih Instansi Pemohon')
                        ->relationship('unitKerja', 'nama_opd')
                        ->searchable()
                        ->preload()
                        ->default(auth()->user()->unit_kerja_id)
                        ->disabled()
                        ->required(),
                ]),

            Step::make('Data Yang Dibutuhkan')
                ->schema([
                    Textarea::make('data_diminta')
                        ->label('Data Yang Dibutuhkan')
                        ->required()
                        ->validationMessages([
                            'required' => 'Mohon jelaskan data yang dibutuhkan',
                        ]),
                ]),

            Step::make('Tujuan Penggunaan Data')
                ->schema([
                    Textarea::make('tujuan_penggunaan')
                        ->label('Tujuan Penggunaan Data')
                        ->required()
                        ->validationMessages([
                            'required' => 'Mohon jelaskan tujuan penggunaan data',
                        ]),
                ]),

            Step::make('Instansi Yang Dituju')
                ->schema([
                    Select::make('opd_tujuan')
                        ->label('Pilih Instansi Tujuan')
                        ->relationship('unitKerjaTujuan', 'nama_opd')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->validationMessages([
                            'required' => 'Mohon pilih instansi tujuan',
                        ]),
                ]),

            Step::make('Upload Dokumen')
                ->schema([
                    FileUpload::make('upload_surat')
                        ->label('📎 Upload Lampiran PDF,Excel,Word,jpg,jpeg,png (Max 2MB)')
                        ->placeholder('📁 Tarik & letakkan file di sini atau klik untuk memilih')
                        ->hintIcon('heroicon-m-question-mark-circle')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'image/jpeg',
                            'image/png',
                        ])

                        ->directory('Surat')
                        ->disk('public')
                        ->maxSize(2048)
                        ->required()
                        ->validationMessages([
                            'required' => 'Mohon upload dokumen pendukung',
                        ]),
                ]),

        ];
    }
}

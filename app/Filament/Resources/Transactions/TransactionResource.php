<?php



namespace App\Filament\Resources\Transactions;



use App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\Transactions\Schemas\TransactionForm;

use App\Filament\Resources\Transactions\Tables\TransactionTable;

use App\Models\Transaction;

use App\Filament\Resources\Products\RelationManagers;



// --- CORE IMPORTS ---

use Filament\Schemas\Schema;

use Filament\Resources\Resource;

use Filament\Tables\Table;

use UnitEnum;

use BackedEnum;



// --- ACTIONS IMPORTS ---

use Filament\Actions\ViewAction;

use Filament\Actions\EditAction;

use Filament\Actions\BulkActionGroup;

use Filament\Actions\DeleteBulkAction;



// --- LAYOUT COMPONENTS (Schema) ---

use Filament\Schemas\Components\Tabs;

use Filament\Schemas\Components\Tabs\Tab;

use Filament\Schemas\Components\Section;

use Filament\Schemas\Components\Grid;



// --- VIEW COMPONENTS (Infolists) ---

use Filament\Infolists\Components\TextEntry;

use Filament\Infolists\Components\RepeatableEntry;

use Filament\Support\Enums\FontWeight;



class TransactionResource extends Resource

{

    protected static ?string $model = Transaction::class;



    protected static ?string $slug = 'transactions';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Penjualan';

    protected static UnitEnum|string|null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 3;



    public static function form(Schema $schema): Schema

    {

        return $schema

            ->schema(TransactionForm::schema())

            ->columns(3);

    }



    // --- METHOD INFOLIST (TAMPILAN DETAIL) ---

    // PENTING: Jika error "Compatibility", ubah "Schema $schema" menjadi "Infolist $infolist"

    public static function infolist(Schema $schema): Schema

    {

        return $schema

            ->schema([

                Tabs::make('Label')

                    ->tabs([

                        // --- TAB 1: Detail Utama ---

                        Tab::make('Detail Transaksi')

                            ->icon('heroicon-m-receipt-percent')

                            ->schema([

                                Section::make()

                                    ->schema([

                                        TextEntry::make('invoice_code')

                                            ->label('No. Invoice')

                                            ->weight(FontWeight::Bold)

                                            ->copyable(),



                                        TextEntry::make('created_at')

                                            ->label('Waktu Transaksi')

                                            ->dateTime('d M Y, H:i'),



                                        TextEntry::make('user.name')

                                            ->label('Customer')

                                            ->icon('heroicon-m-user'),



                                        TextEntry::make('status')

                                            ->badge()

                                            ->color(fn (string $state): string => match ($state) {

                                                'paid' => 'success',

                                                'pending' => 'warning',

                                                'failed' => 'danger',

                                                default => 'gray',

                                            }),



                                        TextEntry::make('payment_method')

                                            ->label('Metode Bayar')

                                            ->badge()

                                            ->color('info'),



                                        TextEntry::make('total_amount')

                                            ->label('Total Bayar')

                                            ->money('IDR')

                                            ->weight(FontWeight::Bold)

                                            ->color('primary')

                                            ->size('lg'),

                                    ])->columns(2),

                            ]),



                        // --- TAB 2: Rincian Barang & Batch (FEFO) ---

                        Tab::make('Rincian Barang & Batch')

                            ->icon('heroicon-m-cube')

                            ->schema([

                                Section::make('Daftar Obat Terjual')

                                    ->description('Informasi batch dan kadaluarsa untuk pelacakan stok (FEFO).')

                                    ->schema([

                                        RepeatableEntry::make('items')

                                            ->label('')

                                            ->schema([

                                                Grid::make(4)

                                                    ->schema([

                                                        TextEntry::make('product.name')

                                                            ->label('Produk')

                                                            ->weight(FontWeight::Bold),



                                                        TextEntry::make('quantity')

                                                            ->label('Quantity'),



                                                        // PERBAIKAN: Menggunakan 'batch.' (Singular) sesuai Model TransactionItem

                                                        TextEntry::make('batch.batch_number')

                                                            ->label('Kode Batch')

                                                            ->icon('heroicon-m-qr-code')

                                                            ->badge()

                                                            ->color('info')

                                                            ->placeholder('Tanpa Batch'),



                                                        // PERBAIKAN: Menggunakan 'batch.' (Singular)

                                                        TextEntry::make('batch.expired_date')

                                                            ->label('Kadaluarsa')

                                                            ->date('d M Y')

                                                            ->weight(FontWeight::Bold)

                                                            ->color(fn ($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : 'success')

                                                            ->icon(fn ($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')

                                                            ->placeholder('-'),

                                                    ]),

                                            ])

                                            ->grid(1)

                                            ->contained(false)

                                    ])

                            ]),

                    ])->columnSpanFull(),

            ]);

    }



    public static function table(Table $table): Table

    {

        return $table

            ->columns(TransactionTable::columns())

            ->actions(array_merge(

                [ViewAction::make()],

                TransactionTable::actions()

            ))

            ->bulkActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make(),

                ]),

            ]);

    }



    public static function getRelations(): array

    {

        return [];

    }



    public static function getPages(): array

    {

        return [

            'index' => Pages\ListTransactions::route('/'),

            'create' => Pages\CreateTransaction::route('/create'),

            'edit' => Pages\EditTransaction::route('/{record}/edit'),

            'view' => Pages\ViewTransaction::route('/{record}'),

        ];

    }

}

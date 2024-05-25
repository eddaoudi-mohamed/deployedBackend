<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ["name", "email", "phone", "status"];

    public function searchableAs(): string
    {
        return 'clients_index';
    }

    public function shouldBeSearchable()
    {
        return $this->status == "available";
    }



    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class, "client_id", 'id')->where('status', '!=', 'deleted');
    }
}

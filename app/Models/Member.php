<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUlidRouteKey;

class Member extends Model
{
    use HasUlidRouteKey;
    protected $fillable = [
        'member_id', 'name', 'email', 'phone', 'upi_id', 'card_number',
        'membership_type', 'card_color', 'balance', 'ball_limit', 'show_balance',
        'valid_until', 'status', 'photo', 'notes', 'is_card_issued', 'has_full_access'
    ];

    protected $casts = [
        'valid_until' => 'date',
        'balance' => 'decimal:2',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function topups()
    {
        return $this->hasMany(Topup::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    public static function generateMemberId()
    {
        $prefix = 'MEM-' . date('Y') . '-';
        $last = self::where('member_id', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($last) {
            $lastNum = (int) substr($last->member_id, -5);
            return $prefix . str_pad($lastNum + 1, 5, '0', STR_PAD_LEFT);
        }
        
        return $prefix . '00001';
    }

    public static function generateCardNumber($membershipType = 'standard')
    {
        // Generate simple sequential card number (easy to remember for service identification)
        // Format: 000001, 000002, 000003, etc. (6 digits)
        
        // RESERVED: Card numbers 000001-000020 are reserved for VIP members only (special personas)
        $reservedRangeStart = 1;
        $reservedRangeEnd = 20;
        
        // If member is VIP, they can get reserved numbers
        if ($membershipType === 'vip') {
            // Check for available reserved numbers first
            // Use database-agnostic approach for SQLite compatibility
            $reservedMembers = self::whereNotNull('card_number')
                ->where('card_number', '!=', '')
                ->get()
                ->filter(function($member) use ($reservedRangeStart, $reservedRangeEnd) {
                    $cardNum = (int) $member->card_number;
                    return $cardNum >= $reservedRangeStart && $cardNum <= $reservedRangeEnd;
                })
                ->pluck('card_number')
                ->map(function($num) {
                    return (int) $num;
                })
                ->toArray();
            
            // Find first available reserved number
            for ($i = $reservedRangeStart; $i <= $reservedRangeEnd; $i++) {
                if (!in_array($i, $reservedMembers)) {
                    return str_pad($i, 6, '0', STR_PAD_LEFT);
                }
            }
        }
        
        // For non-VIP members or if all reserved numbers are taken, start from 21
        $allMembers = self::whereNotNull('card_number')
            ->where('card_number', '!=', '')
            ->get()
            ->filter(function($member) use ($reservedRangeEnd) {
                $cardNum = (int) $member->card_number;
                return $cardNum > $reservedRangeEnd;
            })
            ->sortByDesc(function($member) {
                return (int) $member->card_number;
            })
            ->first();
        
        if ($allMembers && $allMembers->card_number) {
            // Extract the numeric part (e.g., "000021" -> 21)
            $lastNumber = (int) $allMembers->card_number;
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from 21 if no cards exist (reserving 1-20 for VIPs)
            $nextNumber = $reservedRangeEnd + 1;
        }
        
        // Format as: 6 digits with leading zeros (e.g., 000021, 000022, up to 999999)
        return str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    public static function generateUpiId($name)
    {
        $clean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $random = mt_rand(100, 999);
        return $clean . $random . '@golfclub';
    }

    public function canAfford($amount)
    {
        return $this->balance >= $amount;
    }

    public function deductBalance($amount, $description = null)
    {
        if (!$this->canAfford($amount)) {
            return false;
        }
        
        $balanceBefore = $this->balance;
        $this->decrement('balance', $amount);
        
        // Create transaction record
        Transaction::create([
            'transaction_id' => Transaction::generateTransactionId(),
            'member_id' => $this->id,
            'customer_name' => $this->name,
            'type' => 'payment',
            'category' => 'service',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->fresh()->balance,
            'payment_method' => 'balance',
            'notes' => $description,
            'status' => 'completed',
        ]);
        
        return true;
    }

    public function addBalance($amount, $paymentMethod = 'cash', $description = null)
    {
        $balanceBefore = $this->balance;
        $this->increment('balance', $amount);
        
        return [
            'balance_before' => $balanceBefore,
            'balance_after' => $this->fresh()->balance,
        ];
    }
}


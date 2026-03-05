<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuarantorNotification extends Model
{
    protected $fillable = [
        'uploaded_form_id',
        'notification_message',
        'sent_at',
        'sent_to_all_members',
    ];

    protected $casts = [
        'sent_to_all_members' => 'boolean',
        'sent_at' => 'datetime',
    ];

    public function uploadedForm(): BelongsTo
    {
        return $this->belongsTo(UploadedForm::class);
    }

    // Helper methods
    public static function sendToAllMembers(UploadedForm $uploadedForm): self
    {
        $message = self::generateNotificationMessage($uploadedForm);
        
        return self::create([
            'uploaded_form_id' => $uploadedForm->id,
            'notification_message' => $message,
            'sent_at' => now(),
            'sent_to_all_members' => true,
        ]);
    }

    public static function generateNotificationMessage(UploadedForm $uploadedForm): string
    {
        $member = $uploadedForm->member;
        $loanAmount = number_format($uploadedForm->loan_amount, 2);
        $guarantorsRequired = $uploadedForm->guarantors_required;
        
        return "New loan guarantee opportunity: {$member->first_name} {$member->last_name} is requesting a loan of UGX {$loanAmount} and needs {$guarantorsRequired} guarantor(s). Visit your dashboard to view details and provide guarantee.";
    }

    public function getNotificationMessage(): string
    {
        return $this->notification_message;
    }

    public function getFormattedSentAt(): string
    {
        return $this->sent_at->format('M d, Y H:i');
    }
}

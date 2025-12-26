@component('mail::message')
# Database Backup

Your database backup has been created and attached to this email.

**Backup Details:**
- **Date:** {{ now()->format('F j, Y \a\t g:i A') }}
- **File:** {{ $backupName }}

Please store this backup in a safe location.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
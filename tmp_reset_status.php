<?php
use App\Models\User;

$count = User::role(['admin', 'member', 'users'])->update(['status' => 'pending']);
echo "Successfully reset $count users to pending status.\n";

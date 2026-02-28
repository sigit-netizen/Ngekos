<?php
$tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public' ORDER BY tablename");
foreach ($tables as $t) {
    echo $t->tablename . PHP_EOL;
}
echo "---Total: " . count($tables) . " tables---" . PHP_EOL;

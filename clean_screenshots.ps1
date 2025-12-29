$imgs = Get-ChildItem -Path .\screenshots -Recurse -Force -File -Include *.png,*.jpg,*.jpeg,*.webp -ErrorAction SilentlyContinue
$removed = 0
if ($imgs) {
  foreach ($i in $imgs) {
    try { Remove-Item -LiteralPath $i.FullName -Force -ErrorAction Stop; $removed++ } catch { }
  }
}
$remaining = Get-ChildItem -Path .\screenshots -Recurse -Force -File -ErrorAction SilentlyContinue
Write-Output "REMOVED_COUNT:$removed"
Write-Output "REMAINING_COUNT:$($remaining.Count)"
if ($remaining.Count -gt 0) { $remaining | Select-Object FullName,Length } else { Write-Output "NO_FILES_REMAINING" }

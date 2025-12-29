$found = $false
$patterns = @('index.php_login.png','tomselect')
foreach($p in $patterns){
  Write-Output "Searching for pattern: $p"
  try{
    $matches = Get-ChildItem -Path . -Recurse -Force -ErrorAction SilentlyContinue | Where-Object { $_.Name -like "*$p*" }
    if($matches){
      foreach($m in $matches){ Write-Output $m.FullName; $found = $true }
    }
  } catch { }
}
if(-not $found){ Write-Output "NO_MATCHES_FOUND" }

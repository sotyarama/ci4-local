$sh = New-Object -ComObject Shell.Application
$rb = $sh.Namespace(0xA)
$names = @('index.php_login.png','tomselect')
$found = $false
for($i=0; $i -lt $rb.Items().Count; $i++){
  $it = $rb.Items().Item($i)
  foreach($n in $names){
    if($it.Name -like "*$n*"){
      Write-Output ("FOUND: $($it.Name) -> $($it.Path)")
      $found = $true
    }
  }
}
if(-not $found){ Write-Output "NO_MATCHES_FOUND" }

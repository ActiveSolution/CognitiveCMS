function Uninstall-AllModules {
    param(
      [Parameter(Mandatory=$true)]
      [string]$TargetModule,
  
      [Parameter(Mandatory=$true)]
      [string]$Version,
  
      [switch]$Force
    )
  
    $AllModules = @()
  
    'Creating list of dependencies...'
    $target = Find-Module $TargetModule -RequiredVersion $version
    $target.Dependencies | ForEach-Object {
      $AllModules += New-Object -TypeName psobject -Property @{name=$_.name; version=$_.requiredversion}
    }
    $AllModules += New-Object -TypeName psobject -Property @{name=$TargetModule; version=$Version}
  
    foreach ($module in $AllModules) {
      Write-Host ('Uninstalling {0} version {1}' -f $module.name,$module.version)
      try {
        Uninstall-Module -Name $module.name -RequiredVersion $module.version -Force:$Force -ErrorAction Stop
      } catch {
        Write-Host ("`t" + $_.Exception.Message)
      }
    }
  }
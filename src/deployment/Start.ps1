# Required user input

$projectName = Read-Host "What is your project name? (e.g. projectname-environmentname)"
$resourceGroupLocation = "northeurope"

# Azure resource names

$resourceGroupName = "rg-$projectName"
$azureSearchName = "ss-$projectName"
$storageAccountName = "sa-$projectName".Replace('-', '')
$cognitiveServicesName = "cs-$projectName"

$azureSearchIndexName = "index-$projectName"

$storageAccountContainerPrefix = "asccms"
$storageAccountContainerNameDocumentImage = "$storageAccountContainerPrefix-document-image"

# Scripts starts here

# If you need to uninstall AzureRM
# https://github.com/Azure/azure-docs-powershell/blob/master/docs-conceptual/azurermps-5.7.0/uninstall-azurerm-ps.md
# Uninstall-AllModules -TargetModule AzureRM -Version 5.7.0 -Force

if (!$projectName) {
    Write-Host "Please enter Project Name."
    Break
}
if (!$resourceGroupLocation) {
    Write-Host "Please set Resource Group Location."
    Break
}

$azExists = Get-InstalledModule Az -AllVersions -ErrorAction SilentlyContinue

if ($azExists) {
    Write-Host "Az PowerShell installed ..."
}
else {
    Write-Host "You need Az PowerShell https://docs.microsoft.com/en-us/powershell/azure/"
    Break
}

$azSearchExists = Get-InstalledModule Az.Search -AllVersions -ErrorAction SilentlyContinue

if ($azSearchExists) {
    Write-Host "Az.Search PowerShell Module installed ..."
}
else {
    Write-Host "You need Az.Search PowerShell Module https://docs.microsoft.com/en-us/powershell/module/az.search/"
    Break
}

Import-Module Az
Import-Module Az.Search

# https://github.com/Azure/azure-powershell/issues/8628#issuecomment-478644165
$azureContext = Disable-AzContextAutosave -Scope Process
Clear-AzContext
# Clear-AzContext -Scope CurrentUser -Force # https://github.com/Azure/azure-powershell/issues/8628

$azureAccount = Connect-AzAccount

$azureSubscriptions = Get-AzSubscription

$azureSubscriptionCounter = 1
foreach ($azureSubscription in $azureSubscriptions) {
    Write-Output "($azureSubscriptionCounter) $($azureSubscription.Name)"
    $azureSubscriptionCounter++;
}

$subscriptionNumber = Read-Host 'Which subscription?'

$subscription = Select-AzSubscription -SubscriptionId $azureSubscriptions[$subscriptionNumber - 1].Id

$existingFreeAzureSearchResources = Get-AzResourceGroup | ForEach-Object { Get-AzSearchService -ResourceGroupName $_.ResourceGroupName } | Where-Object Sku -eq Free

if ($existingFreeAzureSearchResources) {
    Write-Host "There is already a free Azure Search resource in your subscription ..."
    Break
}

Set-Location $PSScriptRoot
.'.\Scripts\EnsureResourceGroupExists.ps1' -ResourceGroupName $resourceGroupName -ResourceGroupLocation $resourceGroupLocation

.'.\Scripts\DeployAzureResourceGroup.ps1' -ResourceGroupName $resourceGroupName -ResourceGroupLocation $resourceGroupLocation `
                                          -AzureSearchName $azureSearchName -StorageAccountName $storageAccountName `
                                          -StorageAccountContainerNameDocumentImage $storageAccountContainerNameDocumentImage -CognitiveServicesName $cognitiveServicesName

$azureSearchAdminKeyPairs = Get-AzSearchAdminKeyPair -ResourceGroupName $resourceGroupName -ServiceName $azureSearchName

$storageAccountKeys = Get-AzStorageAccountKey -ResourceGroupName $resourceGroupName -AccountName $storageAccountName

$storageAccountConnectionString = "DefaultEndpointsProtocol=https;AccountName=$storageAccountName;AccountKey=$($storageAccountKeys[0].Value);EndpointSuffix=core.windows.net"

$cognitiveServiceKeys = Get-AzCognitiveServicesAccountKey -ResourceGroupName $resourceGroupName -Name $cognitiveServicesName

$createPipelineHeaders = @{
    "api-key" = "$($azureSearchAdminKeyPairs.Primary)"
    "Content-Type" = "application/json"
}

.'.\Scripts\CreateCongnitiveSearchPipeline.ps1' -ProjectName $projectName -StorageAccountContainerNameDocumentImage $storageAccountContainerNameDocumentImage `
                                                -StorageAccountConnectionString $storageAccountConnectionString -AzureSearchName $azureSearchName `
                                                -CreatePipelineHeaders $createPipelineHeaders -CognitiveServiceKey $cognitiveServiceKeys.Key1 `
                                                -AzureSearchIndexName $azureSearchIndexName

# Output
Write-Host "Storage Account Name: $storageAccountName"
Write-Host "Storage Account Access Key1: $($storageAccountKeys[0].Value)"

Write-Host "Azure Search Service Name: $azureSearchName"
Write-Host "Azure Search Admin Key: $($azureSearchAdminKeyPairs.Primary)"
Write-Host "Azure Search Index Name: $azureSearchIndexName"
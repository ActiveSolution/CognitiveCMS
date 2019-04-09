Param(
    [string] [Parameter(Mandatory=$true)] $ResourceGroupName,
    [string] [Parameter(Mandatory=$true)] $ResourceGroupLocation,
    [string] [Parameter(Mandatory=$true)] $AzureSearchName,
    [string] [Parameter(Mandatory=$true)] $StorageAccountName,
    [string] [Parameter(Mandatory=$true)] $StorageAccountContainerNameDocumentImage,
    [string] [Parameter(Mandatory=$true)] $CognitiveServicesName
)

$azureDeployParametersJSON = (Get-Content '.\azuredeploy.parameters.json' -Raw) `
                            -replace '__AzureSearchName__', $azureSearchName `
                            -replace '__ResourceGroupLocation__', $resourceGroupLocation `
                            -replace '__StorageAccountName__', $storageAccountName `
                            -replace '__StorageAccountContainerNameDocumentImage__', $storageAccountContainerNameDocumentImage `
                            -replace '__CognitiveServiceName__', $cognitiveServicesName `

$azureDeployParameters = @{}
(ConvertFrom-Json $azureDeployParametersJSON).parameters.psobject.properties | ForEach-Object { $azureDeployParameters[$_.Name] = $_.Value.Value }

$deployment = New-AzResourceGroupDeployment -ResourceGroupName $resourceGroupName -TemplateFile '.\azuredeploy.json' -TemplateParameterObject $azureDeployParameters
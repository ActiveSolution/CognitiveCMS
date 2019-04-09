Param(
    [string] [Parameter(Mandatory=$true)] $ProjectName,
    [string] [Parameter(Mandatory=$true)] $StorageAccountContainerNameDocumentImage,
    [string] [Parameter(Mandatory=$true)] $StorageAccountConnectionString,
    [string] [Parameter(Mandatory=$true)] $AzureSearchName,
    [System.Collections.Hashtable] [Parameter(Mandatory=$true)] $CreatePipelineHeaders,
    [string] [Parameter(Mandatory=$true)] $CognitiveServiceKey,
    [string] [Parameter(Mandatory=$true)] $AzureSearchIndexName
)

$azureSearchDataSourceName = "datasource-$projectName"
$azureSearchSkillsetName = "skillset-$projectName"
$azureSearchIndexerName = "indexer-$projectName"

# Create DataSource
$createDataSourceJSON = (Get-Content '.\search\1_CreateDataSource.json' -Raw) `
                        -replace '__AzureSearchDataSourceName__', $azureSearchDataSourceName `
                        -replace '__StorageAccountContainerNameDocumentImage__', $storageAccountContainerNameDocumentImage `
                        -replace '__StorageAccountConnectionString__', $storageAccountConnectionString

$createDataSourceUri = "https://$azureSearchName.search.windows.net/datasources?api-version=2017-11-11-Preview"

$azureSearchDatasourcesResponse = Invoke-RestMethod -Uri $createDataSourceUri -Method Post -Headers $createPipelineHeaders -Body $createDataSourceJSON -ErrorAction Continue

# Create Skillset
$createSkillsetJSON = (Get-Content '.\search\2_CreateSkillset.json' -Raw) `
                        -replace '__CognitiveServicesApiKey__', $cognitiveServiceKey

$createSkillsetUri = "https://$azureSearchName.search.windows.net/skillsets/$azureSearchSkillsetName/?api-version=2017-11-11-Preview"

$azureSearchSkillsetResponse = Invoke-RestMethod -Uri $createSkillsetUri -Method Put -Headers $createPipelineHeaders -Body $createSkillsetJSON -ErrorAction Continue

# Create Index
$createIndexJSON = (Get-Content '.\search\3_CreateIndex.json' -Raw)

$createIndexUri = "https://$azureSearchName.search.windows.net/indexes/$azureSearchIndexName/?api-version=2017-11-11-Preview"

$azureSearchIndexResponse = Invoke-RestMethod -Uri $createIndexUri -Method Put -Headers $createPipelineHeaders -Body $createIndexJSON -ErrorAction Continue

# Create Indexer
$createIndexerJSON = (Get-Content '.\search\4_CreateIndexer.json' -Raw) `
                        -replace '__AzureSearchDataSourceName__', $azureSearchDataSourceName `
                        -replace '__AzureSearchSkillsetName__', $azureSearchSkillsetName `
                        -replace '__AzureSearchIndexName__', $azureSearchIndexName `
                        -replace '__AzureSearchIndexerName__', $azureSearchIndexerName

$createIndexerUri = "https://$azureSearchName.search.windows.net/indexers/$azureSearchIndexerName/?api-version=2017-11-11-Preview"

$azureSearchIndexerResponse = Invoke-RestMethod -Uri $createIndexerUri -Method Put -Headers $createPipelineHeaders -Body $createIndexerJSON -ErrorAction Continue
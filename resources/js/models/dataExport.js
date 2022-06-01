class DataExport {

    static create(item) {
        let dataExport = new DataExport()
        dataExport.id = item.id
        dataExport.statement_count = item.statement_count
        dataExport.downloaded_count = item.downloaded_count
        dataExport.filesize = item.filesize
        dataExport.createdAt = item.created_at
        dataExport.updatedAt = item.updated_at
        return dataExport
    }
}

export default DataExport

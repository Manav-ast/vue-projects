export const exportToPDF = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/expenses/export-pdf', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/pdf',
      },
    });

    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.error || 'Failed to export PDF');
    }

    // Get the filename from the Content-Disposition header
    const contentDisposition = response.headers.get('Content-Disposition');
    const filename = contentDisposition
      ? contentDisposition.split('filename=')[1].replace(/"/g, '')
      : 'expenses.pdf';

    // Create a blob from the response
    const blob = await response.blob();

    // Create a temporary link to download the file
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.setAttribute('download', filename);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(link.href);
  } catch (error) {
    console.error('Error exporting PDF:', error);
    throw new Error('Failed to export PDF: ' + error.message);
  }
};

export const exportToCSV = async () => {
  try {
    const response = await fetch('http://localhost:8000/api/expenses/export-csv', {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error('Failed to export CSV');
    }

    const data = await response.json();

    if (data.success && data.file_url) {
      // Create a temporary link to download the file
      const link = document.createElement('a');
      link.href = data.file_url;
      link.setAttribute('download', '');
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    } else {
      throw new Error(data.error || 'Failed to export CSV');
    }
  } catch (error) {
    console.error('Error exporting CSV:', error);
    throw new Error('Failed to export CSV: ' + error.message);
  }
};

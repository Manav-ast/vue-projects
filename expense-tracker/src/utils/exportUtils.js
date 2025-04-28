import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';

export const exportToPDF = (groupedExpenses, groupTotals) => {
  try {
    console.log('Starting PDF export with data:', { groupedExpenses, groupTotals });

    const doc = new jsPDF();

    // Add title
    doc.setFontSize(16);
    doc.text('Expense Report', 14, 15);

    // Add date
    doc.setFontSize(10);
    doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 14, 25);

    // Add summary table
    const summaryData = Object.entries(groupTotals).map(([group, total]) => [
      group,
      new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR',
      }).format(total)
    ]);

    console.log('Summary data for PDF:', summaryData);

    autoTable(doc, {
      startY: 35,
      head: [['Group', 'Total Amount']],
      body: summaryData,
      theme: 'grid',
      headStyles: { fillColor: [41, 128, 185] },
    });

    // Add detailed expenses for each group
    let finalY = doc.lastAutoTable.finalY + 10;

    Object.entries(groupedExpenses).forEach(([group, expenses]) => {
      if (finalY > 250) {
        doc.addPage();
        finalY = 20;
      }

      doc.setFontSize(12);
      doc.text(`Expenses for ${group}`, 14, finalY);

      const expenseData = expenses.map(expense => [
        new Date(expense.date).toLocaleDateString(),
        expense.name,
        new Intl.NumberFormat('en-IN', {
          style: 'currency',
          currency: 'INR',
        }).format(expense.amount)
      ]);

      console.log(`Expense data for group ${group}:`, expenseData);

      autoTable(doc, {
        startY: finalY + 5,
        head: [['Date', 'Name', 'Amount']],
        body: expenseData,
        theme: 'grid',
        headStyles: { fillColor: [52, 152, 219] },
      });

      finalY = doc.lastAutoTable.finalY + 10;
    });

    console.log('PDF generation completed, saving file...');
    doc.save('expense-report.pdf');
    console.log('PDF file saved successfully');
  } catch (error) {
    console.error('Error generating PDF:', error);
    throw new Error('Failed to generate PDF: ' + error.message);
  }
};

export const exportToCSV = (groupedExpenses, groupTotals) => {
  let csvContent = 'data:text/csv;charset=utf-8,';

  // Add summary section
  csvContent += 'Group Summary\n';
  csvContent += 'Group,Total Amount\n';
  Object.entries(groupTotals).forEach(([group, total]) => {
    csvContent += `${group},${total}\n`;
  });

  // Add detailed expenses
  csvContent += '\nDetailed Expenses\n';
  csvContent += 'Group,Date,Name,Amount\n';

  Object.entries(groupedExpenses).forEach(([group, expenses]) => {
    expenses.forEach(expense => {
      csvContent += `${group},${expense.date},${expense.name},${expense.amount}\n`;
    });
  });

  // Create download link
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', 'expense-report.csv');
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

import { useState, useEffect } from 'react'
import { Typography, Grid, Paper, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, CircularProgress, Box } from '@mui/material'
import ReceiptIcon from '@mui/icons-material/Receipt'
import AttachMoneyIcon from '@mui/icons-material/AttachMoney'
import PeopleIcon from '@mui/icons-material/People'
import api from '../api/axios'

export default function Dashboard() {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    api.get('/dashboard')
      .then((res) => setData(res.data.data))
      .catch(() => {})
      .finally(() => setLoading(false))
  }, [])

  if (loading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', mt: 10 }}>
        <CircularProgress />
      </Box>
    )
  }

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        Dashboard
      </Typography>

      <Grid container spacing={3} sx={{ mb: 4 }}>
        <Grid item xs={12} sm={4}>
          <Paper sx={{ p: 3, display: 'flex', alignItems: 'center', gap: 2 }}>
            <ReceiptIcon sx={{ fontSize: 48, color: 'primary.main' }} />
            <div>
              <Typography variant="h3">{data?.total_invoices}</Typography>
              <Typography variant="body2" color="text.secondary">Total Facturas</Typography>
            </div>
          </Paper>
        </Grid>

        <Grid item xs={12} sm={4}>
          <Paper sx={{ p: 3, display: 'flex', alignItems: 'center', gap: 2 }}>
            <AttachMoneyIcon sx={{ fontSize: 48, color: 'success.main' }} />
            <div>
              <Typography variant="h3">${Number(data?.total_facturado).toLocaleString()}</Typography>
              <Typography variant="body2" color="text.secondary">Total Facturado</Typography>
            </div>
          </Paper>
        </Grid>

        <Grid item xs={12} sm={4}>
          <Paper sx={{ p: 3, display: 'flex', alignItems: 'center', gap: 2 }}>
            <PeopleIcon sx={{ fontSize: 48, color: 'secondary.main' }} />
            <div>
              <Typography variant="h3">{data?.total_clients}</Typography>
              <Typography variant="body2" color="text.secondary">Clientes</Typography>
            </div>
          </Paper>
        </Grid>
      </Grid>

      <Typography variant="h6" gutterBottom>
        Últimas Facturas
      </Typography>

      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>Número</TableCell>
              <TableCell>Cliente</TableCell>
              <TableCell>Fecha</TableCell>
              <TableCell>Vencimiento</TableCell>
              <TableCell align="right">Total</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {data?.latest_invoices
              .sort((a, b) => new Date(b.issue_date) - new Date(a.issue_date))
              .map((invoice) => (
              <TableRow key={invoice.id}>
                <TableCell>{invoice.number}</TableCell>
                <TableCell>{invoice.business_name}</TableCell>
                <TableCell>{invoice.issue_date}</TableCell>
                <TableCell>{invoice.due_date}</TableCell>
                <TableCell align="right">${Number(invoice.total).toLocaleString()}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </div>
  )
}

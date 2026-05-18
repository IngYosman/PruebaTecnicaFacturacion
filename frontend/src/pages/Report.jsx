import { useState, useEffect } from 'react'
import {
  Typography,
  Paper,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Box,
  TextField,
  Button,
  CircularProgress,
  MenuItem
} from '@mui/material'
import SearchIcon from '@mui/icons-material/Search'
import api from '../api/axios'

export default function Report() {
  const [invoices, setInvoices] = useState([])
  const [clients, setClients] = useState([])
  const [loading, setLoading] = useState(true)
  const [filterNumber, setFilterNumber] = useState('')
  const [filterClientId, setFilterClientId] = useState('')

  useEffect(() => {
    api.get('/clients')
      .then((res) => setClients(res.data.data))
      .catch(() => {})

    fetchInvoices()
  }, [])

  const fetchInvoices = () => {
    setLoading(true)
    let url = '/invoices'
    const params = []
    if (filterNumber) params.push(`number=${filterNumber}`)
    if (filterClientId) params.push(`client_id=${filterClientId}`)
    if (params.length) url += '?' + params.join('&')

    api.get(url)
      .then((res) => setInvoices(res.data.data))
      .catch(() => {})
      .finally(() => setLoading(false))
  }

  const handleSearch = () => {
    fetchInvoices()
  }

  const handleClear = () => {
    setFilterNumber('')
    setFilterClientId('')
    fetchInvoices()
  }

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
        Informe de Documentos
      </Typography>

      <Paper sx={{ p: 2, mb: 3 }}>
        <Box sx={{ display: 'flex', gap: 2, alignItems: 'center', flexWrap: 'wrap' }}>
          <TextField
            label="Número de factura"
            size="small"
            value={filterNumber}
            onChange={(e) => setFilterNumber(e.target.value)}
            sx={{ width: 200 }}
          />
          <TextField
            select
            label="Cliente"
            size="small"
            value={filterClientId}
            onChange={(e) => setFilterClientId(e.target.value)}
            sx={{ width: 250 }}
          >
            <MenuItem value="">Todos</MenuItem>
            {clients.map((client) => (
              <MenuItem key={client.id} value={client.id}>
                {client.business_name}
              </MenuItem>
            ))}
          </TextField>
          <Button variant="contained" startIcon={<SearchIcon />} onClick={handleSearch}>
            Buscar
          </Button>
          <Button variant="outlined" onClick={handleClear}>
            Limpiar
          </Button>
        </Box>
      </Paper>

      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>Número</TableCell>
              <TableCell>Cliente</TableCell>
              <TableCell>Fecha Emisión</TableCell>
              <TableCell>Vencimiento</TableCell>
              <TableCell align="right">Subtotal</TableCell>
              <TableCell align="right">IVA</TableCell>
              <TableCell align="right">Total</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {invoices.map((invoice) => (
              <TableRow key={invoice.id}>
                <TableCell>{invoice.number}</TableCell>
                <TableCell>{invoice.business_name}</TableCell>
                <TableCell>{invoice.issue_date}</TableCell>
                <TableCell>{invoice.due_date}</TableCell>
                <TableCell align="right">${Number(invoice.subtotal).toLocaleString()}</TableCell>
                <TableCell align="right">{Number(invoice.tax_amount)}%</TableCell>
                <TableCell align="right">${Number(invoice.total).toLocaleString()}</TableCell>
              </TableRow>
            ))}
            {invoices.length === 0 && (
              <TableRow>
                <TableCell colSpan={7} align="center">No se encontraron facturas</TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </TableContainer>
    </div>
  )
}

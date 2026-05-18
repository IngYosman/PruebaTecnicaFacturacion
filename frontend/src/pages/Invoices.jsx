import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import {
  Typography,
  Paper,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Button,
  Box,
  IconButton,
  CircularProgress,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Alert
} from '@mui/material'
import AddIcon from '@mui/icons-material/Add'
import EditIcon from '@mui/icons-material/Edit'
import DeleteIcon from '@mui/icons-material/Delete'
import api from '../api/axios'

export default function Invoices() {
  const [invoices, setInvoices] = useState([])
  const [loading, setLoading] = useState(true)
  const [openDelete, setOpenDelete] = useState(false)
  const [selectedId, setSelectedId] = useState(null)
  const [error, setError] = useState('')
  const navigate = useNavigate()

  useEffect(() => {
    fetchInvoices()
  }, [])

  const fetchInvoices = () => {
    setLoading(true)
    api.get('/invoices')
      .then((res) => setInvoices(res.data.data))
      .catch(() => {})
      .finally(() => setLoading(false))
  }

  const handleDelete = () => {
    api.delete(`/invoices/${selectedId}`)
      .then(() => {
        setOpenDelete(false)
        fetchInvoices()
      })
      .catch((err) => {
        setError(err.response?.data?.message || 'Error al eliminar')
      })
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
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
        <Typography variant="h4">Facturas</Typography>
        <Button variant="contained" startIcon={<AddIcon />} onClick={() => navigate('/invoices/new')}>
          Nueva Factura
        </Button>
      </Box>

      {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}

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
              <TableCell align="center">Acciones</TableCell>
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
                <TableCell align="center">
                  <IconButton size="small" onClick={() => navigate(`/invoices/edit/${invoice.id}`)}>
                    <EditIcon fontSize="small" />
                  </IconButton>
                  <IconButton size="small" color="error" onClick={() => { setSelectedId(invoice.id); setOpenDelete(true) }}>
                    <DeleteIcon fontSize="small" />
                  </IconButton>
                </TableCell>
              </TableRow>
            ))}
            {invoices.length === 0 && (
              <TableRow>
                <TableCell colSpan={8} align="center">No se encontraron facturas</TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </TableContainer>

      <Dialog open={openDelete} onClose={() => setOpenDelete(false)}>
        <DialogTitle>Eliminar Factura</DialogTitle>
        <DialogContent>
          <Typography>¿Está seguro que desea eliminar esta factura?</Typography>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setOpenDelete(false)}>Cancelar</Button>
          <Button color="error" onClick={handleDelete}>Eliminar</Button>
        </DialogActions>
      </Dialog>
    </div>
  )
}

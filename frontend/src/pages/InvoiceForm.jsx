import { useState, useEffect } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import {
  Typography,
  Paper,
  TextField,
  Button,
  Box,
  MenuItem,
  Alert,
  CircularProgress,
  Divider,
  FormLabel
} from '@mui/material'
import api from '../api/axios'

export default function InvoiceForm() {
  const { id } = useParams()
  const navigate = useNavigate()
  const isEdit = !!id

  const [clients, setClients] = useState([])
  const [loading, setLoading] = useState(isEdit)
  const [fieldErrors, setFieldErrors] = useState({})
  const [form, setForm] = useState({
    number: '',
    client_id: '',
    issue_date: '',
    due_date: '',
    payment_method: '',
    subtotal: '',
    tax_amount: '',
    total: '',
    notes: ''
  })

  useEffect(() => {
    api.get('/clients')
      .then((res) => setClients(res.data.data))
      .catch(() => {})

    if (isEdit) {
      api.get(`/invoices/${id}`)
        .then((res) => {
          const inv = res.data.data
          setForm({
            number: inv.number,
            client_id: inv.client_id,
            issue_date: inv.issue_date,
            due_date: inv.due_date,
            payment_method: inv.payment_method || '',
            subtotal: inv.subtotal,
            tax_amount: inv.tax_amount,
            total: inv.total,
            notes: inv.notes || ''
          })
        })
        .catch(() => navigate('/invoices'))
        .finally(() => setLoading(false))
    }
  }, [])

  const handleChange = (e) => {
    const { name, value } = e.target
    let newValue = value

    if (name === 'tax_amount') {
      const num = Number(value)
      if (num > 100) newValue = '100'
      if (num < 0) newValue = '0'
    }

    const newForm = { ...form, [name]: newValue }

    if (name === 'subtotal' || name === 'tax_amount') {
      const sub = name === 'subtotal' ? Number(newValue) || 0 : Number(newForm.subtotal) || 0
      const taxPercent = name === 'tax_amount' ? Number(newValue) || 0 : Number(newForm.tax_amount) || 0
      const taxValue = sub * (taxPercent / 100)
      newForm.total = Math.round(sub + taxValue)
    }

    setForm(newForm)
    if (fieldErrors[name]) {
      setFieldErrors({ ...fieldErrors, [name]: '' })
    }
  }

  const handleSubmit = (e) => {
    e.preventDefault()
    setFieldErrors({})

    const sub = Number(form.subtotal) || 0
    const taxPercent = Number(form.tax_amount) || 0
    const taxValue = sub * (taxPercent / 100)
    const calculatedTotal = Math.round(sub + taxValue)

    const data = {
      number: form.number,
      client_id: Number(form.client_id),
      issue_date: form.issue_date,
      due_date: form.due_date,
      payment_method: form.payment_method,
      notes: form.notes,
      subtotal: sub,
      tax_amount: taxPercent,
      total: calculatedTotal
    }

    const request = isEdit
      ? api.put(`/invoices/${id}`, data)
      : api.post('/invoices', data)

    request
      .then(() => navigate('/invoices'))
      .catch((err) => {
        const errors = err.response?.data?.errors
        if (errors) {
          const formatted = {}
          Object.keys(errors).forEach((key) => {
            formatted[key] = errors[key].join(' ')
          })
          setFieldErrors(formatted)
        } else {
          setFieldErrors({ _general: err.response?.data?.message || 'Error al guardar' })
        }
      })
  }

  if (loading) {
    return (
      <Box sx={{ display: 'flex', justifyContent: 'center', mt: 10 }}>
        <CircularProgress />
      </Box>
    )
  }

  const fieldStyle = { mb: 2 }
  const labelStyle = { mb: 0.5, fontWeight: 500 }

  return (
    <div>
      <Typography variant="h4" gutterBottom>
        {isEdit ? 'Editar Factura' : 'Nueva Factura'}
      </Typography>

      {fieldErrors._general && (
        <Alert severity="error" sx={{ mb: 2 }}>{fieldErrors._general}</Alert>
      )}

      <Paper sx={{ p: 3, maxWidth: 600 }}>
        <form onSubmit={handleSubmit}>
          <FormLabel sx={labelStyle}>Número</FormLabel>
          <TextField
            fullWidth
            name="number"
            value={form.number}
            onChange={handleChange}
            required
            error={!!fieldErrors.number}
            helperText={fieldErrors.number}
            sx={fieldStyle}
          />

          <FormLabel sx={labelStyle}>Cliente</FormLabel>
          <TextField
            fullWidth
            select
            name="client_id"
            value={form.client_id}
            onChange={handleChange}
            required
            error={!!fieldErrors.client_id}
            helperText={fieldErrors.client_id}
            sx={fieldStyle}
          >
            {clients.map((client) => (
              <MenuItem key={client.id} value={client.id}>
                {client.business_name}
              </MenuItem>
            ))}
          </TextField>

          <FormLabel sx={labelStyle}>Fecha de emisión</FormLabel>
          <TextField
            fullWidth
            name="issue_date"
            type="date"
            value={form.issue_date}
            onChange={handleChange}
            required
            error={!!fieldErrors.issue_date}
            helperText={fieldErrors.issue_date}
            InputLabelProps={{ shrink: true }}
            sx={fieldStyle}
          />

          <FormLabel sx={labelStyle}>Fecha de vencimiento</FormLabel>
          <TextField
            fullWidth
            name="due_date"
            type="date"
            value={form.due_date}
            onChange={handleChange}
            required
            error={!!fieldErrors.due_date}
            helperText={fieldErrors.due_date}
            InputLabelProps={{ shrink: true }}
            sx={fieldStyle}
          />

          <FormLabel sx={labelStyle}>Método de pago</FormLabel>
          <TextField
            fullWidth
            name="payment_method"
            value={form.payment_method}
            onChange={handleChange}
            error={!!fieldErrors.payment_method}
            helperText={fieldErrors.payment_method}
            sx={fieldStyle}
          />

          <Divider sx={{ my: 2 }} />

          <FormLabel sx={labelStyle}>Subtotal</FormLabel>
          <TextField
            fullWidth
            name="subtotal"
            type="number"
            value={form.subtotal}
            onChange={handleChange}
            required
            error={!!fieldErrors.subtotal}
            helperText={fieldErrors.subtotal}
            sx={fieldStyle}
          />

          <FormLabel sx={labelStyle}>IVA (%)</FormLabel>
          <TextField
            fullWidth
            name="tax_amount"
            type="number"
            inputProps={{ min: 0, max: 100, step: 1 }}
            value={form.tax_amount}
            onChange={handleChange}
            required
            error={!!fieldErrors.tax_amount}
            helperText={fieldErrors.tax_amount}
            sx={fieldStyle}
          />

          <FormLabel sx={labelStyle}>Total</FormLabel>
          <TextField
            fullWidth
            name="total"
            type="number"
            value={form.total}
            InputProps={{ readOnly: true }}
            sx={fieldStyle}
          />

          <Divider sx={{ my: 2 }} />

          <FormLabel sx={labelStyle}>Observaciones</FormLabel>
          <TextField
            fullWidth
            name="notes"
            value={form.notes}
            onChange={handleChange}
            multiline
            rows={3}
            sx={{ mb: 3 }}
          />

          <Box sx={{ display: 'flex', gap: 2 }}>
            <Button type="submit" variant="contained">
              {isEdit ? 'Actualizar' : 'Crear'}
            </Button>
            <Button variant="outlined" onClick={() => navigate('/invoices')}>
              Cancelar
            </Button>
          </Box>
        </form>
      </Paper>
    </div>
  )
}

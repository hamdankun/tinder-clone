/**
 * Register Screen
 *
 * User registration screen
 * - Name, email, password input
 * - Form validation
 * - Loading states
 * - Error handling
 * - Navigation to login
 */

import React, { useState } from 'react';
import {
  View,
  StyleSheet,
  TouchableOpacity,
  Alert,
  TextInput,
  ScrollView,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { NativeStackScreenProps } from '@react-navigation/native-stack';
import { Lucide } from '@react-native-vector-icons/lucide';
import { useRegister } from '../../hooks/useAuth';
import Button from '../../components/atoms/Button';
import Text from '../../components/atoms/Text';
import { COLORS, SPACING, FONT_SIZES } from '../../config/constants';

type RegisterScreenProps = NativeStackScreenProps<any, 'Register'>;

interface FormData {
  name: string;
  email: string;
  age: string;
  location: string;
  password: string;
  confirmPassword: string;
}

export const RegisterScreen: React.FC<RegisterScreenProps> = ({
  navigation,
}) => {
  const [formData, setFormData] = useState<FormData>({
    name: '',
    email: '',
    age: '',
    location: '',
    password: '',
    confirmPassword: '',
  });
  const [errors, setErrors] = useState<Partial<FormData>>({});
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirmPassword, setShowConfirmPassword] = useState(false);

  const registerMutation = useRegister();

  const validateForm = (): boolean => {
    const newErrors: Partial<FormData> = {};

    if (!formData.name) {
      newErrors.name = 'Name is required';
    } else if (formData.name.length < 2) {
      newErrors.name = 'Name must be at least 2 characters';
    }

    if (!formData.email) {
      newErrors.email = 'Email is required';
    } else if (!formData.email.includes('@')) {
      newErrors.email = 'Invalid email format';
    }

    if (!formData.age) {
      newErrors.age = 'Age is required';
    } else if (parseInt(formData.age, 10) < 18) {
      newErrors.age = 'Must be at least 18 years old';
    }

    if (!formData.location) {
      newErrors.location = 'Location is required';
    }

    if (!formData.password) {
      newErrors.password = 'Password is required';
    } else if (formData.password.length < 6) {
      newErrors.password = 'Password must be at least 6 characters';
    }

    if (!formData.confirmPassword) {
      newErrors.confirmPassword = 'Please confirm password';
    } else if (formData.password !== formData.confirmPassword) {
      newErrors.confirmPassword = 'Passwords do not match';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleRegister = async () => {
    if (!validateForm()) {
      return;
    }

    try {
      await registerMutation.mutateAsync({
        name: formData.name,
        email: formData.email,
        age: parseInt(formData.age, 10),
        location: formData.location,
        password: formData.password,
        password_confirmation: formData.confirmPassword,
      });

      // Success - user is now logged in
      // Navigation happens automatically via RootNavigator
    } catch (error: any) {
      Alert.alert(
        'Registration Failed',
        error?.response?.data?.message ||
          'Unable to create account. Please try again.',
        [{ text: 'OK' }],
      );
    }
  };

  return (
    <SafeAreaView style={styles.safeArea}>
      <ScrollView>
        <View style={styles.content}>
          {/* Back Button */}
          <TouchableOpacity
            style={styles.backButton}
            onPress={() => navigation.goBack()}
          >
            <Lucide
              name="arrow-left"
              size={FONT_SIZES.lg}
              color={COLORS.text}
            />
          </TouchableOpacity>

          {/* Header */}
          <View style={styles.header}>
            <Text style={styles.title}>💫 Create Account</Text>
            <Text style={styles.subtitle}>Join to start matching</Text>
          </View>

          {/* Form */}
          <View style={styles.form}>
            {/* Name Input */}
            <View style={styles.inputContainer}>
              <Text style={styles.label}>Full Name</Text>
              <TextInput
                style={[styles.input, errors.name && styles.inputError]}
                placeholder="Enter your full name"
                placeholderTextColor={COLORS.placeholder}
                value={formData.name}
                onChangeText={text => {
                  setFormData({ ...formData, name: text });
                  if (errors.name) {
                    setErrors({ ...errors, name: undefined });
                  }
                }}
                editable={!registerMutation.isPending}
              />
              {errors.name && (
                <Text style={[styles.errorText, { color: COLORS.danger }]}>
                  {errors.name}
                </Text>
              )}
            </View>

            {/* Email Input */}
            <View style={styles.inputContainer}>
              <Text style={styles.label}>Email</Text>
              <TextInput
                style={[styles.input, errors.email && styles.inputError]}
                placeholder="Enter your email"
                placeholderTextColor={COLORS.placeholder}
                value={formData.email}
                onChangeText={text => {
                  setFormData({ ...formData, email: text });
                  if (errors.email) {
                    setErrors({ ...errors, email: undefined });
                  }
                }}
                keyboardType="email-address"
                autoCapitalize="none"
                editable={!registerMutation.isPending}
              />
              {errors.email && (
                <Text style={[styles.errorText, { color: COLORS.danger }]}>
                  {errors.email}
                </Text>
              )}
            </View>

            {/* Age Input */}
            <View style={styles.inputContainer}>
              <Text style={styles.label}>Age</Text>
              <TextInput
                style={[styles.input, errors.age && styles.inputError]}
                placeholder="Enter your age"
                placeholderTextColor={COLORS.placeholder}
                value={formData.age}
                onChangeText={text => {
                  setFormData({ ...formData, age: text });
                  if (errors.age) {
                    setErrors({ ...errors, age: undefined });
                  }
                }}
                keyboardType="number-pad"
                editable={!registerMutation.isPending}
              />
              {errors.age && (
                <Text style={[styles.errorText, { color: COLORS.danger }]}>
                  {errors.age}
                </Text>
              )}
            </View>

            {/* Location Input */}
            <View style={styles.inputContainer}>
              <Text style={styles.label}>Location</Text>
              <TextInput
                style={[styles.input, errors.location && styles.inputError]}
                placeholder="Enter your location"
                placeholderTextColor={COLORS.placeholder}
                value={formData.location}
                onChangeText={text => {
                  setFormData({ ...formData, location: text });
                  if (errors.location) {
                    setErrors({ ...errors, location: undefined });
                  }
                }}
                editable={!registerMutation.isPending}
              />
              {errors.location && (
                <Text style={[styles.errorText, { color: COLORS.danger }]}>
                  {errors.location}
                </Text>
              )}
            </View>

            {/* Password Input */}
            <View style={styles.inputContainer}>
              <Text style={styles.label}>Password</Text>
              <View style={styles.passwordInputWrapper}>
                <TextInput
                  style={[
                    styles.input,
                    styles.passwordInput,
                    errors.password && styles.inputError,
                  ]}
                  placeholder="Enter password (min 6 chars)"
                  placeholderTextColor={COLORS.placeholder}
                  value={formData.password}
                  onChangeText={text => {
                    setFormData({ ...formData, password: text });
                    if (errors.password) {
                      setErrors({ ...errors, password: undefined });
                    }
                  }}
                  secureTextEntry={!showPassword}
                  editable={!registerMutation.isPending}
                />
                <TouchableOpacity
                  style={styles.passwordIconButton}
                  onPress={() => setShowPassword(!showPassword)}
                >
                  <Lucide
                    name={showPassword ? 'eye-off' : 'eye'}
                    size={FONT_SIZES.lg}
                    color={COLORS.placeholder}
                  />
                </TouchableOpacity>
              </View>
              {errors.password && (
                <Text style={[styles.errorText, { color: COLORS.danger }]}>
                  {errors.password}
                </Text>
              )}
            </View>

            {/* Confirm Password Input */}
            <View style={styles.inputContainer}>
              <Text style={styles.label}>Confirm Password</Text>
              <View style={styles.passwordInputWrapper}>
                <TextInput
                  style={[
                    styles.input,
                    styles.passwordInput,
                    errors.confirmPassword && styles.inputError,
                  ]}
                  placeholder="Confirm password"
                  placeholderTextColor={COLORS.placeholder}
                  value={formData.confirmPassword}
                  onChangeText={text => {
                    setFormData({ ...formData, confirmPassword: text });
                    if (errors.confirmPassword) {
                      setErrors({ ...errors, confirmPassword: undefined });
                    }
                  }}
                  secureTextEntry={!showConfirmPassword}
                  editable={!registerMutation.isPending}
                />
                <TouchableOpacity
                  style={styles.passwordIconButton}
                  onPress={() => setShowConfirmPassword(!showConfirmPassword)}
                >
                  <Lucide
                    name={showConfirmPassword ? 'eye-off' : 'eye'}
                    size={FONT_SIZES.lg}
                    color={COLORS.placeholder}
                  />
                </TouchableOpacity>
              </View>
              {errors.confirmPassword && (
                <Text style={[styles.errorText, { color: COLORS.danger }]}>
                  {errors.confirmPassword}
                </Text>
              )}
            </View>
          </View>

          {/* Register Button */}
          <Button
            label={
              registerMutation.isPending ? 'Creating account...' : 'Sign Up'
            }
            variant="primary"
            size="large"
            onPress={handleRegister}
            disabled={registerMutation.isPending}
          />

          {/* Login Link */}
          <View style={styles.footer}>
            <Text style={styles.footerText}>Already have an account? </Text>
            <TouchableOpacity onPress={() => navigation.navigate('Login')}>
              <Text style={styles.footerLink}>Login</Text>
            </TouchableOpacity>
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
};

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: COLORS.white,
  },
  container: {
    flex: 1,
  },
  content: {
    flex: 1,
    padding: SPACING.lg,
    justifyContent: 'space-between',
  },
  backButton: {
    alignSelf: 'flex-start',
    paddingVertical: SPACING.sm,
    paddingHorizontal: SPACING.sm,
    marginBottom: SPACING.md,
  },
  header: {
    marginTop: SPACING.lg,
  },
  title: {
    fontSize: FONT_SIZES.xxxl,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  subtitle: {
    fontSize: FONT_SIZES.md,
    color: COLORS.text,
    marginTop: SPACING.sm,
    opacity: 0.6,
  },
  form: {
    gap: SPACING.lg,
    marginBottom: SPACING.lg,
  },
  inputContainer: {
    gap: SPACING.sm,
  },
  label: {
    fontSize: FONT_SIZES.md,
    fontWeight: '600',
    color: COLORS.text,
  },
  input: {
    borderWidth: 1,
    borderColor: COLORS.border,
    borderRadius: 8,
    padding: SPACING.md,
    flex: 1,
    backgroundColor: COLORS.light,
    minHeight: 48,
    fontSize: FONT_SIZES.md,
    color: COLORS.text,
  },
  passwordInputWrapper: {
    position: 'relative',
    flexDirection: 'row',
    alignItems: 'center',
  },
  passwordInput: {
    paddingRight: SPACING.xl + SPACING.md,
  },
  passwordIconButton: {
    position: 'absolute',
    right: SPACING.md,
    paddingHorizontal: SPACING.sm,
    paddingVertical: SPACING.sm,
    justifyContent: 'center',
    alignItems: 'center',
  },
  inputError: {
    borderColor: COLORS.danger,
    backgroundColor: 'rgba(255, 107, 107, 0.05)',
  },
  errorText: {
    fontSize: FONT_SIZES.sm,
  },
  footer: {
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    gap: SPACING.sm,
    marginBottom: SPACING.lg,
  },
  footerText: {
    fontSize: FONT_SIZES.md,
    color: COLORS.text,
    opacity: 0.6,
  },
  footerLink: {
    fontSize: FONT_SIZES.md,
    color: COLORS.primary,
    fontWeight: 'bold',
  },
});
